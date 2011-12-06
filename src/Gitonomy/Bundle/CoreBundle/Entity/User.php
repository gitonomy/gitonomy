<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
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
     * @Assert\NotBlank(message="Select a username")
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

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
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

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function regenerateSalt()
    {
        return $this->salt = md5(uniqid().microtime());
    }

    public function getSshKeys()
    {
        return $this->sshKeys;
    }

    public function getRepositories()
    {
        return $this->repositories;
    }

    public function equals(UserInterface $user)
    {
        return $user->getUsername() == $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }
}
