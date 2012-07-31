<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Security\Core\Role\Role as SecurityRole;

/**
 * Role-representation of Gitonomy.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Role extends Base\BaseRole
{
    public function __construct()
    {
        parent::__construct();

        $this->isGlobal = true;
    }
    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Adds a permission to the role.
     *
     * @param Permission $permission A permission object
     */
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
}
