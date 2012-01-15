<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

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

        foreach ($this->getRole()->getPermissions() as $permission) {
            $name = $permission->getName();
            $roles[$name] = new ProjectRole($project, $name);
        }

        return $roles;
    }
}
