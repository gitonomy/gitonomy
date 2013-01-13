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

class Email
{
    protected $id;

    /** @var User */
    protected $user;
    protected $email;
    protected $isDefault;
    protected $activationToken;

    public function __construct(User $user, $email = null, $isActive = false)
    {
        $this->user      = $user;
        $this->email     = $email;
        $this->isDefault = false;

        if (!$isActive) {
            $this->createActivationToken();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isDefault()
    {
        return $this->isDefault;
    }

    public function setDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    public function createActivationToken()
    {
        return $this->activationToken = md5(microtime().uniqid());
    }

    /**
     * @throws LogicException           Email is already active
     * @throws InvalidArgumentException ActivationToken value is not correct
     */
    public function validateActivationToken($activationToken)
    {
        if (null === $this->activationToken) {
            throw new \LogicException('This e-mail is already active');
        }

        if ($this->activationToken !== $activationToken) {
            throw new \InvalidArgumentException('ActivationToken not correct');
        }

        $this->activationToken = null;

        return true;
    }

    public function isActive()
    {
        return null === $this->activationToken;
    }

    public function activate()
    {
        $this->activationToken = null;
    }

    public function disactivate()
    {
        $this->createActivationToken();
    }

    public function getActivationToken()
    {
        return $this->activationToken;
    }
}
