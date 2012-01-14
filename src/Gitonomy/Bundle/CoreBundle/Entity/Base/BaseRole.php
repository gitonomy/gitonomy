<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Base;

use Doctrine\Common\Collections\ArrayCollection;

abstract class BaseRole
{
    protected $id;
    protected $name;
    protected $description;
    protected $isGlobal;
    protected $permissions;
    protected $userRolesProject;
    protected $gitAccesses;
    protected $usersGlobal;

    public function __construct()
    {
        $this->permissions      = new ArrayCollection();
        $this->userRolesProject = new ArrayCollection();
        $this->users            = new ArrayCollection();
        $this->gitAccesses      = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getIsGlobal()
    {
        return $this->isGlobal;
    }

    public function setIsGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setPermissions(ArrayCollection $permissions)
    {
        $this->permissions = $permissions;
    }

    public function getUserRolesProject()
    {
        return $this->userRolesProject;
    }

    public function setUserRolesProject(ArrayCollection $userRolesProject)
    {
        $this->userRolesProject = $userRolesProject;
    }

    public function getGitAccesses()
    {
        return $this->gitAccesses;
    }

    public function setGitAccesses(ArrayCollection $gitAccesses)
    {
        $this->gitAccesses = $gitAccesses;
    }

    public function getUsersGlobal()
    {
        return $this->usersGlobal;
    }

    public function setUsersGlobal(ArrayCollection $usersGlobal)
    {
        $this->usersGlobal = $usersGlobal;
    }
}
