<?php

namespace Gitonomy\Bundle\CoreBundle\Debug;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;
use Gitonomy\Bundle\GitBundle\DataCollector\GitDataCollector;
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
            $this->collector->addRepository($repository);
        }

        return $repository;
    }
}
