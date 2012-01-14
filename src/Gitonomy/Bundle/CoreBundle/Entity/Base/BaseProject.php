<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Base;

use Doctrine\Common\Collections\ArrayCollection;

abstract class BaseProject
{
    protected $id;
    protected $name;
    protected $slug;
    protected $repositorySize;
    protected $userRoles;
    protected $gitAccesses;

    public function __construct()
    {
        $this->userRoles   = new ArrayCollection();
        $this->gitAccesses = new ArrayCollection();
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

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getRepositorySize()
    {
        return $this->repositorySize;
    }

    public function setRepositorySize($repositorySize)
    {
        $this->repositorySize = $repositorySize;
    }

    public function getUserRoles()
    {
        return $this->userRoles;
    }

    public function setUserRoles(ArrayCollection $userRoles)
    {
        $this->userRoles = $userRoles;
    }

    public function getGitAccesses()
    {
        return $this->gitAccesses;
    }

    public function setGitAccesses(ArrayCollection $gitAccesses)
    {
        $this->gitAccesses = $gitAccesses;
    }
}
