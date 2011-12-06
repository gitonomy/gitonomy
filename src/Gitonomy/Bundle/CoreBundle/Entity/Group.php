<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="group")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="text",length=64)
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Role", inversedBy="groups")
     * @ORM\JoinTable(name="group_role")
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\User", inversedBy="groups")
     * @ORM\JoinTable(name="group_user")
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="Gitonomy\Bundle\CoreBundle\Entity\Project", mappedBy="groups")
     */
    protected $projects;

    public function __construct()
    {
        $this->roles    = new ArrayCollection();
        $this->users    = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }
}
