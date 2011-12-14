<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_role_global", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_role", columns={"user_id", "role_id"})
 * })
 *
 * @AssertDoctrine\UniqueEntity(fields={"project", "user"},groups={"admin"})
 */
class UserRoleGlobal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\User", inversedBy="userRolesGlobal")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Role", inversedBy="userRolesGlobal")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    protected $role;

    public function __toString()
    {
        return sprintf('%s is %s', $this->getUser(), $this->getRole());
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
}
