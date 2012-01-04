<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertDoctrine;

/**
 * @ORM\Entity()
 * @ORM\Table(name="project_git_access")
 */
class ProjectGitAccess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project", inversedBy="gitAccesses")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Role", inversedBy="gitAccesses")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    protected $role;

    /**
     * @ORM\Column(type="string",length=128)
     * @Assert\NotBlank()
     */
    protected $reference;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isRead;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isWrite;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isAdmin;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function getIsRead()
    {
        return $this->isRead;
    }

    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }

    public function getIsWrite()
    {
        return $this->isWrite;
    }

    public function setIsWrite($isWrite)
    {
        $this->isWrite = $isWrite;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
}
