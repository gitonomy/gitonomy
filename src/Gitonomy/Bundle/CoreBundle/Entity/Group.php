<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="group_")
 *
 * @todo To fix Doctrine2, we use name "group_" instead of group
 */
class Group
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
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Role", inversedBy="groups")
     * @ORM\JoinTable(name="group_role")
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\User", inversedBy="groups")
     * @ORM\JoinTable(name="group_user")
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project", mappedBy="groups")
     */
    protected $projects;

    public function __construct()
    {
        $this->roles    = new ArrayCollection();
        $this->users    = new ArrayCollection();
        $this->projects = new ArrayCollection();
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

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;
    }

    public function addRole(Role $role)
    {
        $this->roles->add($role);
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers(ArrayCollection $users)
    {
        $this->users = $users;
    }

    public function addUser(User $user)
    {
        $this->users->add($user);
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function setProjects(ArrayCollection $projects)
    {
        $this->projects = $projects;
    }

    public function addProject(Project $project)
    {
        $this->projects->add($project);
    }
}
