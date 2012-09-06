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

use Gitonomy\Bundle\CoreBundle\Security\ProjectRole;

class UserRoleProject
{
    protected $id;
    protected $user;
    protected $role;
    protected $project;

    public function __construct(User $user = null, Project $project = null, Role $role = null)
    {
        $this->user    = $user;
        $this->project = $project;
        $this->role    = $role;
    }

    public function getSecurityRoles()
    {
        $roles   = array();
        $project = $this->getProject();

        foreach ($this->role->getPermissions() as $permission) {
            $name = $permission->getName();
            $roles[] = new ProjectRole($project, $name);
        }

        return $roles;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    public function isRole(Role $role)
    {
        return $this->role->getId() == $role->getId();
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }
}
