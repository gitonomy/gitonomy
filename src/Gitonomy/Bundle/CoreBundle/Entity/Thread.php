<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Thread
{
    protected $id;
    protected $project;
    protected $user;
    protected $reference;
    protected $messages;

    public function __construct(Project $project, User $user, $reference, ArrayCollection $messages = null)
    {
        $this
            ->setProject($project)
            ->setUser($user)
            ->setReference($reference)
            ->setMessages($messages)
        ;
    }

    public function getId()
    {
        return $id;
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

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages(ArrayCollection $messages = null)
    {
        $this->messages = $messages ? $messages : new ArrayCollection();

        return $this;
    }

    public function addMessage(ThreadMessage $message)
    {
        $this->messages->add($message);
    }
}
