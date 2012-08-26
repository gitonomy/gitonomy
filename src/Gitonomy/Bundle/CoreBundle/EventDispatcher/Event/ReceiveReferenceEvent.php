<?php

namespace Gitonomy\Bundle\CoreBundle\EventDispatcher\Event;

use Gitonomy\Git\ReceiveReference;

use Symfony\Component\EventDispatcher\Event;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class ReceiveReferenceEvent extends Event
{
    protected $project;
    protected $user;
    protected $reference;

    public function __construct(Project $project, User $user, ReceiveReference $reference)
    {
        $this->project   = $project;
        $this->user      = $project;
        $this->reference = $project;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }
}
