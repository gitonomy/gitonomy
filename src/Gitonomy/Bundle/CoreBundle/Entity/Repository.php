<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Gitonomy\Bundle\CoreBundle\Repository\RepositoryRepository")
 * @ORM\Table(name="repository")
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
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id",nullable=true)
     */
    protected $owner;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project",inversedBy="repositories")
     * @ORM\JoinColumn(name="project_id",referencedColumnName="id")
     */
    protected $project;
}
