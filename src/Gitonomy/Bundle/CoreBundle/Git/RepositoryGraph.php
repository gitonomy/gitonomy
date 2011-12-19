<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Predis\Client;

use Gitonomy\Git\Repository;
use Gitonomy\Git\Commit;

class RepositoryGraph
{
    protected $repository;
    protected $client;

    function __construct(Repository $repository, Client $client)
    {
        $this->repository = $repository;
        $this->client     = $client;
    }
}
