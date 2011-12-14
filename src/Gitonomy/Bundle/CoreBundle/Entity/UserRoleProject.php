<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_role_project", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_project", columns={"user_id", "project_id"})
 * })
 *
 * @AssertDoctrine\UniqueEntity(fields={"project", "user"},groups={"admin"})
 */
class UserRoleProject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\User", inversedBy="userRolesProject")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Role", inversedBy="userRolesProject")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project", inversedBy="userRoles")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     */
    protected $project;

    public function __toString()
    {
        return sprintf('%s is %s in the project %s', $this->getUser(), $this->getRole(), $this->getProject());
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }
}
