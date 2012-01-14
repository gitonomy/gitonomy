<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Base;

use Gitonomy\Bundle\CoreBundle\Entity\User;

abstract class BaseEmail
{
    protected $id;
    protected $user;
    protected $email;
    protected $isDefault;
    protected $activation;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getIsDefault()
    {
        return $this->isDefault;
    }

    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    public function isDefault()
    {
        return $this->isDefault;
    }

    public function getActivation()
    {
        return $this->activation;
    }

    public function setActivation($activation)
    {
        $this->activation = $activation;
    }
}
