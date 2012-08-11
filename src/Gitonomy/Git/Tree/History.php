<?php

namespace Gitonomy\Git\Tree;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Tree;

class History
{
    /**
     * Original hashes, used to detect modification. Indexed by filename
     *
     * @var array
     */
    protected $hashes;

    /**
     * Indexed by filename, contains the last commit modifying resource
     *
     * @var array
     */
    protected $resolution;

    public function __construct(Commit $commit, $path)
    {
        $tree = $commit->getTree()->resolvePath($path);
        if (!$tree instanceof Tree) {
            throw new \InvalidArgumentException(sprintf('Path "%s" does not reference a tree', $path));
        }

        $this->hashes     = array();
        $this->resolution = array();
        $date = $commit->getCommitterDate();
        foreach ($tree->getEntries() as $name => $data) {
            $this->hashes[$name]     = $data[1]->getHash();
            $this->resolution[$name] = null;
        }

        $this->visit($commit, $path);

        foreach ($this->resolution as $name => $value) {
            if (null === $value) {
                $this->resolution[$name] = $this;
            }
        }
    }

    public function isSolved()
    {
        foreach ($this->resolution as $val) {
            if (null === $val) {
                return false;
            }
        }

        return true;
    }

    private function visit(Commit $commit, $path)
    {
        $tree = $commit->getTree()->resolvePath($path);

        foreach ($tree->getEntries() as $name => $data) {
            if (isset($this->hashes[$name])) {
                continue;
            }
            $hash = $data[1]->getHash();
            if (null !== $this->resolution[$name]) {
                continue;
            }

            if ($hash !== $this->hashes[$name]) {
                $this->resolution[$name] = $commit;
            }
        }

        if ($this->isSolved()) {
            return;
        }

        foreach ($commit->getParents() as $parent) {
            $this->visit($parent, $path);
        }

    }

    public function find($name)
    {
        if (!isset($this->hashes[$name])) {
            throw new \InvalidArgumentException(sprintf('No entry named "%s"', $name));
        }

        return $this->resolution[$name];
    }
}
