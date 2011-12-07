<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_role")
 */
class UserRole
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\User", inversedBy="userRoles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Role", inversedBy="userRoles")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project", inversedBy="userRoles")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id",nullable=true)
     */
    protected $project;
}
