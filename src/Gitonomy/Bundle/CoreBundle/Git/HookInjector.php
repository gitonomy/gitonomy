<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;

class HookInjector
{
    protected $hooks;

    /**
     * @var Gitonomy\Bundle\CoreBundle\Git\RepositoryPool
     */
    protected $repositoryPool;

    public function __construct(RepositoryPool $repositoryPool, array $hooks)
    {
        $this->hooks = $hooks;
        $this->repositoryPool = $repositoryPool;
    }

    public function onProjectCreate(ProjectEvent $event)
    {
        $repository = $this->repositoryPool->getGitRepository($event->getProject());
        $hooks = $repository->getHooks();

        foreach ($this->hooks as $name => $file) {
            $hooks->setSymlink($name, $file);
        }
    }
}
