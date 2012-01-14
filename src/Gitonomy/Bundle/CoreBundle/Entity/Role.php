<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class Role extends Base\BaseRole
{
    public function __toString()
    {
        return $this->name;
    }

    public function addPermission(Permission $permission)
    {
        $this->permissions->add($permission);
    }
}
