<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="text",length=32)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Permission", inversedBy="roles")
     * @ORM\JoinTable(name="role_permission")
     */
    protected $permissions;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project", mappedBy="roles")
     */
    protected $groups;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->groups      = new ArrayCollection();
    }
}
