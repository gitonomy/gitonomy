<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Security\Core\Role\Role as SecurityRole;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Role
{
    protected $id;
    protected $name;
    protected $slug;
    protected $description;
    protected $isGlobal;

    /**
     * @var ArrayCollection
     */
    protected $permissions;

    /**
     * @var ArrayCollection
     */
    protected $userRolesProject;

    /**
     * @var ArrayCollection
     */
    protected $gitAccesses;

    public function __construct($name = null, $slug = null, $description = null, $isGlobal = true)
    {
        $this->permissions      = new ArrayCollection();
        $this->userRolesProject = new ArrayCollection();
        $this->gitAccesses      = new ArrayCollection();

        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->isGlobal = $isGlobal;
    }

    public function addPermission(Permission $permission)
    {
        $this->permissions->add($permission);
    }

    /**
     * Returns the security objects.
     *
     * @throws LogicException Throws an exception when role is not global.
     */
    public function getSecurityRoles()
    {
        if (false === $this->isGlobal) {
            throw new \LogicException('Cannot generate security roles of a non-global role');
        }

        $roles = array();
        foreach ($this->permissions as $permission) {
            $roles[] = new SecurityRole($permission->getName());
        }

        return $roles;
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

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function isGlobal()
    {
        return $this->isGlobal;
    }

    public function setGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function getUserRolesProject()
    {
        return $this->userRolesProject;
    }

    public function getGitAccesses()
    {
        return $this->gitAccesses;
    }
}
