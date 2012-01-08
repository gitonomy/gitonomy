<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;

/**
 * @ORM\Entity(repositoryClass="Gitonomy\Bundle\CoreBundle\Repository\ProjectRepository")
 * @ORM\Table(name="project")
 *
 * @AssertDoctrine\UniqueEntity(fields="name",groups={"admin"})
 * @AssertDoctrine\UniqueEntity(fields="slug",groups={"admin"})
 */
class Project
{
    const SLUG_PATTERN = '[a-z0-9-_]+';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=32,unique=true)
     * @Assert\NotBlank(groups={"admin"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string",length=32,unique=true)
     * @Assert\NotBlank(groups={"admin"})
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess", mappedBy="project", cascade={"persist", "remove"}))
     */
    protected $gitAccesses;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $repositorySize;

    public function __construct()
    {
        $this->userRoles   = new ArrayCollection();
        $this->gitAccesses = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getRepositorySize()
    {
        return $this->repositorySize;
    }

    public function setRepositorySize($repositorySize)
    {
        $this->repositorySize = $repositorySize;
    }

    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * Returns the user role of a given user.
     *
     * @return Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject The user role on the project
     *
     * @throws InvalidArgumentException Throws an exception if no role was found for the given user on the project.
     */
    public function getUserRole(User $user)
    {
        foreach ($this->userRoles as $userRole) {
            if ($user->equals($userRole->getUser())) {
                return $userRole;
            }
        }

        throw new \InvalidArgumentException('No role for user on project');
    }

    public function getGitAccesses()
    {
        return $this->gitAccesses;
    }
}
