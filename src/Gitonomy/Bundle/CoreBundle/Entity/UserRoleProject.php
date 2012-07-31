<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Gitonomy\Bundle\CoreBundle\Security\ProjectRole;

class UserRoleProject extends Base\BaseUserRoleProject
{
    public function __toString()
    {
        return sprintf('%s is %s in the project %s', $this->getUser(), $this->getRole(), $this->getProject());
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
}
