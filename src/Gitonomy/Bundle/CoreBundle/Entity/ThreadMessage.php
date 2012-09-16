<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class ThreadMessage
{
    const TYPE_MESSAGE    = 0;
    const TYPE_CLOSE      = 1;
    const TYPE_GIT_COMMIT = 2;
    const TYPE_GIT_PR     = 3;
    const TYPE_GIT_MERGE  = 4;

    protected $id;
    protected $thread;
    protected $user;
    protected $message;
    protected $type;
    protected $publishedAt;

    public function __construct(Thread $thread, User $user, $type, $message, $publishedAt = null)
    {
        $this
            ->setThread($thread)
            ->setUser($user)
            ->setType($type)
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

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        if (!in_array($type, self::getTypes())) {
            throw new \Exception(sprintf('Type "%s" not allowed', $type));
        }

        $this->type = $type;

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

    protected static function getTypes()
    {
        return array(
            self::TYPE_MESSAGE,
            self::TYPE_CLOSE,
            self::TYPE_GIT_COMMIT,
            self::TYPE_GIT_PR,
            self::TYPE_GIT_MERGE,
        );
    }
}
