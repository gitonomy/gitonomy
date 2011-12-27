<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="email", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="email", columns={"email"})
 * })
 *
 * @AssertDoctrine\UniqueEntity(fields="email",groups={"registration", "admin"})
 */
class Email
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="emails")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="string",length=256,unique=true, nullable=false)
     *
     * @Assert\NotBlank(groups={"registration", "admin"})
     */
    protected $email;

    /**
     * @ORM\Column(name="is_default", type="boolean")
     *
     * @Assert\NotBlank(groups={"admin"})
     */
    protected $isDefault;

    /**
     * @ORM\Column(type="string",length=128, nullable=true))
     */
    protected $activation;

    public function __toString()
    {
        return $this->email;
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

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getIsDefault()
    {
        return $this->isDefault;
    }

    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    public function isDefault()
    {
        return $this->isDefault;
    }

    public function getActivation()
    {
        return $this->activation;
    }

    public function setActivation($activation)
    {
        $this->activation = $activation;
    }
}
