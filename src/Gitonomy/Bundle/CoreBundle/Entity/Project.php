<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project")
 */
class Project
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
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Repository", mappedBy="project")
     */
    protected $repositories;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="projects")
     * @ORM\JoinTable(name="project_group")
     */
    protected $groups;

    public function __construct()
    {
        $this->repositories = new ArrayCollection();
        $this->groups       = new ArrayCollection();
    }
}
