<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Predis\Client;

use Gitonomy\Git\Repository;
use Gitonomy\Git\Commit;

class RepositoryGraphPool
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(Repository $repository)
    {
        return new RepositoryGraph($repository, $this->client);
    }
}
