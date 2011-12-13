<?php

namespace Gitonomy\Git;

class Reference
{
    protected $repository;
    protected $name;
    protected $commitHash;

    function __construct($repository, $name, $commitHash)
    {
        $this->repository = $repository;
        $this->name = $name;
        $this->commitHash = $commitHash;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCommit()
    {
        return $this->repository->getCommit($this->commitHash);
    }

    public function getLastModification()
    {
        return $this->getCommit()->getAuthorDate();
    }
}