<?php

namespace Gitonomy\Bundle\CoreBundle\Debug;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;

use Gitonomy\Git\Event\Events;

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
            $repository->addListener(Events::PRE_COMMAND,  array($this->collector, 'onPreCommand'));
            $repository->addListener(Events::POST_COMMAND, array($this->collector, 'onPostCommand'));
        }

        return $repository;
    }
}
