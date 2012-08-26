<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class ThreadMessage
{
    protected $id;
    protected $thread;
    protected $user;
    protected $message;
    protected $publishedAt;

    public function __construct(Thread $thread, User $user, $message, $publishedAt = null)
    {
        $this
            ->setThread($thread)
            ->setUser($user)
            ->setMessage($message)
            ->setPublishedAt($publishedAt)
        ;
    }

    public function getId()
    {
        return $id;
    }

    public function getThread()
    {
        return $this->thread;
    }

    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

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

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTime $publishedAt = null)
    {
        $this->publishedAt = $publishedAt ? $publishedAt : new \DateTime();

        return $this;
    }
}
