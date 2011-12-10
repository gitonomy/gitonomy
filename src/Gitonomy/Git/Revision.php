<?php

namespace Gitonomy\Git;

class Revision
{
    protected $repository;
    protected $name;
    protected $resolved;

    public function __construct(Repository $repository, $name)
    {
        $this->repository = $repository;
        $this->name       = $name;
    }

    public function getLog($limit = null)
    {
        return new Log($this->repository, $this->getResolved(), $limit);
    }

    public function getResolved()
    {
        if (null !== $this->resolved) {
            return $this->resolved;
        }

        ob_start();
        system(sprintf(
            'cd %s && git rev-parse --verify %s',
            escapeshellarg($this->repository->getPath()),
            escapeshellarg($this->name)
        ), $result);
        $output = ob_get_clean();

        if (0 !== $result) {
            throw new \RuntimeException(sprintf('Unable to resolve the revision "%s"', $this->name));
        }

        return $this->resolved = trim($output);
    }

    public function getCommit($limit = null)
    {
        return $this->repository->getCommit($this->getResolved());
    }
}
