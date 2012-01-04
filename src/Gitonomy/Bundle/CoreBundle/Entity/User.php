<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="Gitonomy\Bundle\CoreBundle\Repository\UserRepository")
 *
 * @ORM\Table(name="user", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="username", columns={"username"})
 * })
 *
 * @AssertDoctrine\UniqueEntity(fields="username",groups={"registration", "admin"})
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
     * @Assert\NotBlank(groups={"registration", "admin", "change_username"})
     * @Assert\MinLength(limit=3,groups={"registration", "admin", "change_username"})
     * @Assert\MaxLength(limit=32,groups={"registration", "admin", "change_username"})
     * @Assert\Regex(pattern="/^[a-zA-Z0-9][a-zA-Z0-9-_]+[a-zA-Z0-9]$/",groups={"registration", "admin", "change_username"},message="Only letters, numbers, -, _")
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
     * @ORM\Column(type="string",length=64)
     *
     * @Assert\NotBlank(groups={"registration", "profile_informations"})
     * @Assert\Choice(callback={"DateTimeZone","listIdentifiers"},groups={"registration", "profile_informations", "admin"})
     */
    protected $timezone;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $forgotPasswordToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $forgotPasswordCreatedAt;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $activationToken;

    /**
     * @ORM\OneToMany(targetEntity="UserSshKey", mappedBy="user", cascade={"persist", "remove"}))
     */
    protected $sshKeys;

    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user", cascade={"persist", "remove"}))
     */
    protected $emails;

    /**
     * @ORM\OneToMany(targetEntity="UserRoleProject", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $projectRoles;

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
        $this->emails          = new ArrayCollection();
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

    public function getId()
    {
        return $this->id;
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

    public function getSalt()
    {
        return $this->salt;
    }

    public function getSshKeys()
    {
        return $this->sshKeys;
    }

    public function getProjectRoles()
    {
        return $this->projectRoles;
    }

    public function setProjectRoles($projectRoles)
    {
        $this->projectRoles = $projectRoles;
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

    public function getEmails()
    {
        return $this->emails;
    }

    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    public function addEmail(Email $email)
    {
        if (!$this->hasDefaultEmail()) {
            $email->setIsDefault(true);
        }

        $email->setUser($this);
        $this->emails->add($email);
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

    public function getDefaultEmail()
    {
        foreach ($this->getEmails() as $email) {
            if ($email->isDefault()) {
                return $email;
            }
        }
    }

    public function hasDefaultEmail()
    {
        return null !== $this->getDefaultEmail();
    }

    public function setDefaultEmail(Email $email)
    {
        if ($this->hasDefaultEmail()) {
            throw new \LogicException(sprintf('User "%s" has already a default email address : "%s"', $this, $this->getDefaultEmail()));
        }

        $email->setUser($this);
        $email->setIsDefault(true);
        $this->addEmail($email);
    }

    public function getForgotPasswordToken()
    {
        return $this->forgotPasswordToken;
    }

    public function setForgotPasswordToken($forgotPasswordToken)
    {
        $this->forgotPasswordToken = $forgotPasswordToken;
    }

    public function getForgotPasswordCreatedAt()
    {
        return $this->forgotPasswordCreatedAt;
    }

    public function setForgotPasswordCreatedAt(\DateTime $forgotPasswordCreatedAt)
    {
        $this->forgotPasswordCreatedAt = $forgotPasswordCreatedAt;
    }

    public function createForgotPasswordToken()
    {
        $this->forgotPasswordToken     = md5(uniqid().microtime());
        $this->forgotPasswordCreatedAt = new \DateTime();
    }

    public function removeForgotPasswordToken()
    {
        $this->forgotPasswordToken     = null;
        $this->forgotPasswordCreatedAt = null;
    }

    public function isForgotPasswordTokenExpired()
    {
        $max = clone $this->forgotPasswordCreatedAt;
        $max->add(new \DateInterval('P2D')); // 2 days
        $now = new \DateTime();

        return $now->getTimestamp() > $max->getTimeStamp();
    }

    public function markAllKeysAsUninstalled()
    {
        foreach ($this->sshKeys as $sshKey) {
            $sshKey->setIsInstalled(false);
        }
    }

    public function getActivationToken()
    {
        return $this->activationToken;
    }

    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;
    }

    public function generateActivationToken()
    {
        $this->activationToken = md5(uniqid().microtime());
    }

    public function removeActivationToken()
    {
        $this->activationToken = null;
    }

    public function isActived()
    {
        return (null !== $this->password && null !== $this->activationToken);
    }
}
