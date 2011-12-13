<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission")
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=50,unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean", name="is_global", nullable=false)
     */
    protected $isGlobal;

    /**
     * @ORM\ManyToOne(targetEntity="Permission", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Permission", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Role", mappedBy="permissions")
     */
    protected $roles;

    public function __construct()
    {
        $this->roles    = new ArrayCollection();
        $this->isGlobal = false;
    }

    public function __toString()
    {
        return $this->name;
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

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function hasParent()
    {
        return ($this->parent instanceof Permission);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}
