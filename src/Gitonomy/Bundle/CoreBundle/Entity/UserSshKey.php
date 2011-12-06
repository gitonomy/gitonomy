<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Gitonomy\Bundle\CoreBundle\Repository\UserSshKeyRepository")
 * @ORM\Table(name="user_ssh_key")
 */
class UserSshKey
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\User", inversedBy="sshKeys")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isInstalled = false;

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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getIsInstalled()
    {
        return $this->isInstalled;
    }

    public function setIsInstalled($isInstalled)
    {
        $this->isInstalled = $isInstalled;
    }
}
