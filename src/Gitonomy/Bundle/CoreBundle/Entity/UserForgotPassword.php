<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\CoreBundle\Entity;

class UserForgotPassword
{
    protected $id;
    protected $token;
    protected $createdAt;
    protected $user;

    public function __construct(User $user, $token = null, \DateTime $createdAt = null)
    {
        $this->user      = $user;
        $this->setToken($token, $createdAt);
    }

    public function setToken($token = null, \DateTime $createdAt = null)
    {
        $this->token     = null === $token ? md5(uniqid().microtime()) : $token;
        $this->createdAt = null === $createdAt ? new \DateTime() : $createdAt;
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

        return $now > $max;
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
