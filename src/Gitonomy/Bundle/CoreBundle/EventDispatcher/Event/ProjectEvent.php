<?php

namespace Gitonomy\Bundle\CoreBundle\EventDispatcher\Event;

use Symfony\Component\EventDispatcher\Event;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

class ProjectEvent extends Event
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
