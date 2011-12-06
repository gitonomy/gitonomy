<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(type="string",length=32)
     */
    protected $username;

    /**
     * @ORM\Column(type="string",length=128)
     */
    protected $password;

    /**
     * @ORM\Column(type="string",length=32)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string",length=64)
     */
    protected $fullname;

    /**
     * @ORM\Column(type="string",length=256)
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\UserSshKey", mappedBy="user")
     */
    protected $sshKeys;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Repository", mappedBy="owner")
     */
    protected $repositories;

    public function __construct()
    {
        $this->sshKeys      = new ArrayCollection();
        $this->repositories = new ArrayCollection();
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
        return array('ROLE_USER');
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
}
