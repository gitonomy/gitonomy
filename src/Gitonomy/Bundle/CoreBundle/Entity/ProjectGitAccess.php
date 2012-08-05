<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class ProjectGitAccess
{
    protected $id;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Role
     */
    protected $role;

    protected $reference;
    protected $isRead;
    protected $isWrite;
    protected $isAdmin;

    public function __construct(Project $project, Role $role = null, $reference = '*',
        $isRead = false, $isWrite = false, $isAdmin = false)
    {
        $this->project   = $project;
        $this->role      = $role;
        $this->reference = $reference;
        $this->isRead    = $isRead;
        $this->isWrite   = $isWrite;
        $this->isAdmin   = $isAdmin;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProject()
    {
        return $this->project;
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

    public function isRead()
    {
        return $this->isRead;
    }

    public function setRead($isRead)
    {
        $this->isRead = $isRead;
    }

    public function isWrite()
    {
        return $this->isWrite;
    }

    public function setWrite($isWrite)
    {
        $this->isWrite = $isWrite;
    }

    public function isAdmin()
    {
        return $this->isAdmin;
    }

    public function setAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
}
