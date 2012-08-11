<?php

namespace Gitonomy\Git;

/**
 * Reference in a Git repository.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class Reference
{
    /**
     * Current repository.
     *
     * @var Gitonomy\Git\Repository
     */
    protected $repository;

    /**
     * Fullname reference.
     *
     * @var string
     */
    protected $fullname;

    /**
     * Hash of the commit.
     *
     * @var string
     */
    protected $commitHash;

    /**
     * Constructor.
     *
     * @param Gitonomy\Git\Repository $repository A repository object
     *
     * @param string $fullname Fullname of the reference
     *
     * @param string $commitHash The commit hash
     */
    public function __construct($repository, $fullname, $commitHash)
    {
        $this->repository = $repository;
        $this->fullname   = $fullname;
        $this->commitHash = $commitHash;
    }

    /**
     * Returns the fullname of the reference.
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Returns the commit associated to the reference.
     *
     * @return Gitonomy\Git\Commit
     */
    public function getCommit()
    {
        return $this->repository->getCommit($this->commitHash);
    }

    /**
     * Returns the last modification date of the reference.
     *
     * @return DateTime
     */
    public function getLastModification()
    {
        return $this->getCommit()->getAuthorDate();
    }
}
