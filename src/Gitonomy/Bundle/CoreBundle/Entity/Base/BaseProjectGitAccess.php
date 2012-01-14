<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Base;

use Doctrine\Common\Collections\ArrayCollection;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\Role;

abstract class BaseProjectGitAccess
{
    protected $id;
    protected $project;
    protected $role;
    protected $reference;
    protected $isRead;
    protected $isWrite;
    protected $isAdmin;

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

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function getIsRead()
    {
        return $this->isRead;
    }

    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }

    public function getIsWrite()
    {
        return $this->isWrite;
    }

    public function setIsWrite($isWrite)
    {
        $this->isWrite = $isWrite;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
}
