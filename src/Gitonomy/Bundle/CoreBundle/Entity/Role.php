<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;

/**
 * @ORM\Entity(repositoryClass="Gitonomy\Bundle\CoreBundle\Repository\RoleRepository")
 * @ORM\Table(name="role")
 *
 * @AssertDoctrine\UniqueEntity(fields="name",groups={"admin"})
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
     * @ORM\Column(type="string",length=50,unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string",length=50)
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Permission", inversedBy="roles")
     * @ORM\JoinTable(name="role_permission")
     */
    protected $permissions;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isGlobal;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject", mappedBy="role")
     */
    protected $userRolesProject;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="userRolesGlobal")
     */
    protected $usersGlobal;

    public function __construct()
    {
        $this->permissions      = new ArrayCollection();
        $this->userRolesProject = new ArrayCollection();
        $this->users            = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
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

    public function getUserRolesProject()
    {
        return $this->userRolesProject;
    }

    public function getUserGlobal()
    {
        return $this->userGlobal;
    }

    public function getIsGlobal()
    {
        return $this->isGlobal;
    }

    public function setIsGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;
    }
}
