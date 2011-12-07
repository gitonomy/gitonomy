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
     * @ORM\OneToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\UserRole", mappedBy="project")
     */
    protected $userRoles;

    public function __construct()
    {
        $this->repositories = new ArrayCollection();
        $this->userRoles    = new ArrayCollection();
    }
}
