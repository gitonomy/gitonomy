<?php

namespace Gitonomy\Bundle\CoreBundle\Debug;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;

use Gitonomy\Git\Repository;

class DebugRepositoryPool extends RepositoryPool
{
    protected $collector;

    public function setDataCollector(GitDataCollector $collector)
    {
        $this->collector = $collector;
    }

    public function getGitRepository(Project $project)
    {
        $repository = parent::getGitRepository($project);

        if ($this->collector) {
            $repository->setLogger($this->collector->createLogger($repository));
        }

        return $repository;
    }
}
