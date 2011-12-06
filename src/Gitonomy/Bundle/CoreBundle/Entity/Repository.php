<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Repository object.
 *
 * @ORM\Entity(repositoryClass="Gitonomy\Bundle\CoreBundle\Entity\RepositoryRepository")
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Repository
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\User",inversedBy="repositories")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $owner;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $namespace;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Repository", inversedBy="forks")
     * @ORM\JoinColumn(name="fork_repository_id", referencedColumnName="id",nullable=true)
     */
    protected $forkedFrom;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Repository", mappedBy="forkedFrom")
     */
    protected $forks;

    public function __construct()
    {
        $this->forks = new ArrayCollection();
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getForkedFrom()
    {
        return $this->forkedFrom;
    }

    public function setForkedFrom(Repository $forkedFrom)
    {
        $this->forkedFrom = $forkedFrom;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getForks()
    {
        return $this->forks;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getIsForked()
    {
        return $this->forkedFrom !== null;
    }
}
