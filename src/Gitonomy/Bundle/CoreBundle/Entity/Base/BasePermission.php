<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Base;

use Doctrine\Common\Collections\ArrayCollection;

use Gitonomy\Bundle\CoreBundle\Entity\Permission;

abstract class BasePermission
{
    protected $id;
    protected $name;
    protected $isGlobal;
    protected $parent;
    protected $children;
    protected $roles;

    public function __construct()
    {
        $this->roles    = new ArrayCollection();
        $this->children = new ArrayCollection();
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

    public function getIsGlobal()
    {
        return $this->isGlobal;
    }

    public function setIsGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Permission $parent)
    {
        $this->parent = $parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;
    }
}
