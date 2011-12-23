<?php

namespace Gitonomy\Bundle\CoreBundle\EventListener\Event;

use Symfony\Component\EventDispatcher\Event;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

class ProjectCreateEvent extends Event
{
    protected $project;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }
}