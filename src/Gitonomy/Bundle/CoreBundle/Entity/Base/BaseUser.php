<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Base;

use Doctrine\Common\Collections\ArrayCollection;

use Gitonomy\Bundle\CoreBundle\Entity\UserForgotPassword;

abstract class BaseUser
{
    protected $id;
    protected $username;
    protected $password;
    protected $salt;
    protected $fullname;
    protected $timezone;
    protected $activationToken;
    protected $sshKeys;
    protected $emails;
    protected $projectRoles;
    protected $globalRoles;
    protected $forgotPassword;

    public function __construct()
    {
        $this->sshKeys      = new ArrayCollection();
        $this->repositories = new ArrayCollection();
        $this->globalRoles  = new ArrayCollection();
        $this->emails       = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getSshKeys()
    {
        return $this->sshKeys;
    }

    public function setSshKeys(ArrayCollection $sshKeys)
    {
        $this->sshKeys = $sshKeys;
    }

    public function getProjectRoles()
    {
        return $this->projectRoles;
    }

    public function setProjectRoles(ArrayCollection $projectRoles)
    {
        $this->projectRoles = $projectRoles;
    }

    public function getGlobalRoles()
    {
        return $this->globalRoles;
    }

    public function setGlobalRoles($globalRoles)
    {
        $this->globalRoles = $globalRoles;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getEmails()
    {
        return $this->emails;
    }

    public function setEmails(ArrayCollection $emails)
    {
        $this->emails = $emails;
    }

    public function getActivationToken()
    {
        return $this->activationToken;
    }

    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;
    }

    public function getForgotPassword()
    {
        return $this->forgotPassword;
    }

    public function setForgotPassword(UserForgotPassword $forgotPassword)
    {
        $this->forgotPassword = $forgotPassword;
    }
}
