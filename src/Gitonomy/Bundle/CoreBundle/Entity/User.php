<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
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
}
