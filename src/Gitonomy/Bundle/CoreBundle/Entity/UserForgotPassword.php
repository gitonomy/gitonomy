<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class UserForgotPassword
{
    protected $id;
    protected $token;
    protected $createdAt;
    protected $user;

    public function __construct(User $user, $token = null)
    {
        $this->user      = $user;
        $this->token     = null === $token ? md5(uniqid().microtime()) : $token;
        $this->createdAt = new \DateTime();
    }

    /**
     * Tests if the forgot password token is expired.
     *
     * @return boolean Result of test
     */
    public function isTokenExpired()
    {
        $max = clone $this->createdAt;
        $max->add(new \DateInterval('P2D')); // 2 days
        $now = new \DateTime();

        return $now->getTimestamp() > $max->getTimeStamp();
    }

    public function validateToken($token)
    {
        if ($this->isTokenExpired()) {
            throw new \OutOfRangeException('Token expired');
        }

        if ($this->token !== $token) {
            throw new \InvalidArgumentException('Token invalid');
        }

        $this->token = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUser()
    {
        return $this->user;
    }
}
