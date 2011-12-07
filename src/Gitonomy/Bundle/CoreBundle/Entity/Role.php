<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="text",length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="text",length=50)
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Permission", inversedBy="roles")
     * @ORM\JoinTable(name="role_permission")
     */
    protected $permissions;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project", mappedBy="roles")
     */
    protected $groups;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->groups      = new ArrayCollection();
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

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setPermissions(ArrayCollection $permissions)
    {
        $this->permissions = $permissions;
    }

    public function addPermission(Permission $permission)
    {
        $this->permissions->add($permission);
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroups(ArrayCollection $groups)
    {
        $this->groups = $groups;
    }

    public function addGroup(Group $group)
    {
        $this->groups->add($group);
    }
}
