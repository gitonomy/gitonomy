<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Thread
{
    protected $id;
    protected $project;
    protected $reference;
    protected $messages;

    public function __construct(Project $project, $reference = null)
    {
        $this->project   = $project;
        $this->reference = $reference;
    }

    public function getId()
    {
        return $id;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
