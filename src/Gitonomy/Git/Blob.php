<?php

namespace Gitonomy\Git;

/**
 * Representation of a Blob commit.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Blob
{
    protected $repository;
    protected $hash;
    protected $initialized = false;

    public function __construct(Repository $repository, $hash)
    {
        $this->repository = $repository;
        $this->hash = $hash;
    }

    protected function initialize()
    {
    }
}
