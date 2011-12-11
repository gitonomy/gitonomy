<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gitonomy\Bundle\CoreBundle\Validator\Constraints as GitonomyAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
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
     * @GitonomyAssert\Unique(groups={"registration", "admin"})
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
     * @GitonomyAssert\Unique(groups={"registration", "admin"})
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
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Repository", mappedBy="owner")
     */
    protected $repositories;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\UserRole", mappedBy="user")
     */
    protected $userRoles;

    public function __construct()
    {
        $this->sshKeys      = new ArrayCollection();
        $this->repositories = new ArrayCollection();
        $this->userRoles    = new ArrayCollection();
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

    public function getRepositories()
    {
        return $this->repositories;
    }

    public function setRepositories(ArrayCollection $repositories)
    {
        $this->repositories = $repositories;
    }

    public function addRepository(Repository $repository)
    {
        $this->repositories->add($repository);
    }

    public function getGlobalRole()
    {
        foreach ($this->getUserRoles() as $userRole)
        {
            if ($userRole->isGlobal()) {
                return $userRole->getRole();
            }
        }
    }

    public function getGlobalPermissions()
    {
        $permissions = array();

        $globalRole = $this->getGlobalRole();
        if (null === $globalRole) {
            return $permissions;
        }

        foreach ($globalRole->getPermissions() as $permission) {
            if ($permission->hasParent()) {
                $perm = $permission->getParent()->getPermission();
                $permissions[$perm] = $perm;
            }

            $perm = $permission->getPermission();
            $permissions[$perm] = $perm;
        }

        return $permissions;
    }

    public function getUserRoles()
    {
        return $this->userRoles;
    }

    public function setUserRoles(UserRole $userRoles)
    {
        $this->userRoles = $userRoles;
    }

    public function addUserRole(UserRole $userRole)
    {
        $this->userRoles->add($userRole);
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }
}
