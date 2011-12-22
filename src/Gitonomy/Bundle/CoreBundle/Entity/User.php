<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="username", columns={"username"})
 * })
 *
 * @AssertDoctrine\UniqueEntity(fields="username",groups={"registration", "admin"})
 * @AssertDoctrine\UniqueEntity(fields="email",groups={"registration", "admin"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=32,unique=true)
     *
     * @Assert\NotBlank(groups={"registration", "admin"})
     * @Assert\MinLength(limit=3,groups={"registration", "admin"})
     * @Assert\MaxLength(limit=32,groups={"registration", "admin"})
     * @Assert\Regex(pattern="/[a-zA-Z0-9][a-zA-Z0-9-_]+[a-zA-Z0-9]/",groups={"registration", "admin"})
     */
    protected $username;

    /**
     * @ORM\Column(type="string",length=128, nullable=true)
     *
     * @Assert\NotBlank(groups={"registration"})
     */
    protected $password;

    /**
     * @ORM\Column(type="string",length=32)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string",length=64)
     *
     * @Assert\NotBlank(groups={"registration", "profile_informations", "admin"})
     */
    protected $fullname;

    /**
     * @ORM\Column(type="string",length=256,unique=true)
     *
     * @Assert\NotBlank(groups={"registration", "admin"})
     */
    protected $email;

    /**
     * @ORM\Column(type="string",length=64)
     *
     * @Assert\NotBlank(groups={"registration", "profile_informations"})
     * @Assert\Choice(callback={"DateTimeZone","listIdentifiers"},groups={"registration", "profile_informations", "admin"})
     */
    protected $timezone;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\UserSshKey", mappedBy="user")
     */
    protected $sshKeys;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $userRolesProject;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="user_role_global")
     */
    protected $userRolesGlobal;

    public function __construct()
    {
        $this->sshKeys         = new ArrayCollection();
        $this->repositories    = new ArrayCollection();
        $this->userRolesGlobal = new ArrayCollection();
        $this->regenerateSalt();
    }

    public function __toString()
    {
        return $this->fullname;
    }

    public function equals(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        return $user->getUsername() === $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return array_merge($this->getGlobalPermissions(), array('AUTHENTICATED' => 'AUTHENTICATED'));
    }

    public function regenerateSalt()
    {
        return $this->salt = md5(uniqid().microtime());
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getSshKeys()
    {
        return $this->sshKeys;
    }

    public function getUserRolesProject()
    {
        return $this->userRolesProject;
    }

    public function getUserRolesGlobal()
    {
        return $this->userRolesGlobal;
    }

    public function setUserRolesGlobal($userRolesGlobal)
    {
        $this->userRolesGlobal = $userRolesGlobal;
    }

    public function addUserRoleGlobal(Role $userRoleGlobal)
    {
        $this->userRolesGlobal->add($userRoleGlobal);
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getGlobalPermissions()
    {
        $permissions = array();

        foreach ($this->getUserRolesGlobal() as $userRole) {
            foreach ($userRole->getPermissions() as $permission) {
                if ($permission->hasParent()) {
                    $perm = $permission->getParent()->getName();
                    $permissions[$perm] = $perm;
                }

                $perm = $permission->getName();
                $permissions[$perm] = $perm;
            }
        }

        return $permissions;
    }
}
