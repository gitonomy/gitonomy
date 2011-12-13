<?php

namespace Gitonomy\Git;

/**
 * Reference set associated to a repository.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ReferenceBag
{
    /**
     * Repository object.
     *
     * @var Gitonomy\Git\Repository
     */
    protected $repository;

    /**
     * Associative array of fullname references.
     *
     * @var array
     */
    protected $references;

    /**
     * List with all tags
     *
     * @var array
     */
    protected $tags;

    /**
     * List with all branches
     *
     * @var array
     */
    protected $branches;

    /**
     * A boolean indicating if the bag is already initialized.
     *
     * @var boolean
     */
    protected $initialized;

    /**
     * Constructor.
     *
     * @param Gitonomy\Git\Repository $repository The repository
     */
    function __construct($repository)
    {
        $this->repository  = $repository;
        $this->initialized = false;
        $this->references  = array();
        $this->tags        = array();
        $this->branches    = array();
    }

    /**
     * Returns a reference, by name.
     *
     * @param string $fullname Fullname of the reference (refs/heads/master, for example).
     *
     * @return Gitonomy\Git\Reference A reference object.
     */
    public function get($fullname)
    {
        $this->initialize();

        if (!isset($this->references[$fullname])) {
            throw new \RuntimeException(sprintf('Reference "%s" not found', $fullname));
        }

        return $this->references[$fullname];
    }

    /**
     * Returns all tags.
     *
     * @return array
     */
    public function getTags()
    {
        $this->initialize();

        return $this->tags;
    }

    /**
     * Returns all branches.
     *
     * @return array
     */
    public function getBranches()
    {
        $this->initialize();

        $result = array();
        foreach ($this->references as $reference) {
            if ($reference instanceof Reference\Branch) {
                $result[] = $reference;
            }
        }

        return $result;
    }

    /**
     * Returns a given tag.
     *
     * @param string $name Name of the tag
     *
     * @return Gitonomy\Git\Reference\Tag
     */
    public function getTag($name)
    {
        $this->initialize();

        return $this->get('refs/tags/'.$name);
    }

    /**
     * Returns a given branch.
     *
     * @param string $name Name of the branch
     *
     * @return Gitonomy\Git\Reference\Branch
     */
    public function getBranch($name)
    {
        $this->initialize();

        return $this->get('refs/heads/'.$name);
    }

    protected function initialize()
    {
        ob_start();
        system(sprintf(
            'cd %s && git show-ref',
            escapeshellarg($this->repository->getPath())
        ), $return);
        $result = ob_get_clean();

        if (0 !== $return) {
            throw new \RuntimeException('Error while getting list of references');
        }

        $parser = new Parser\ReferenceParser();
        $parser->parse($result);

        foreach ($parser->references as $row) {
            list($commitHash, $fullname) = $row;

            if (preg_match('#^refs/heads/(.*)$#', $fullname, $vars)) {
                $reference = new Reference\Branch($this->repository, $fullname, $commitHash);
                $this->references[$fullname] = $reference;
                $this->branches[] = $reference;
            } elseif (preg_match('#^refs/tags/(.*)$#', $fullname, $vars)) {
                $reference = new Reference\Tag($this->repository, $fullname, $commitHash);
                $this->references[$fullname] = $reference;
                $this->tags[] = $reference;
            } else {
                throw new \RuntimeException(sprintf('Unknown reference name: "%s"', $fullname));
            }
        }

        return $result;
    }
}
