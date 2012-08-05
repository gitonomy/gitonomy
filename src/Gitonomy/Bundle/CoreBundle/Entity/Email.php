<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class Email
{
    protected $id;

    /** @var User */
    protected $user;
    protected $email;
    protected $isDefault;
    protected $activationToken;

    public function __construct(User $user, $email = null)
    {
        $this->user  = $user;
        $this->email = $email;

        $this->isDefault = false;
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

    public function getActivationToken()
    {
        return $this->activationToken;
    }
}
