<?php

namespace Gitonomy\Bundle\CoreBundle\EventDispatcher\Event;

use Symfony\Component\EventDispatcher\Event;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class ProjectEvent extends Event
{
    protected $project;
    protected $user;

    public function __construct(Project $project, User $user = null)
    {
        $this->project = $project;
        $this->user    = $user;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function hasUser()
    {
        return null !== $this->user;
    }
}
