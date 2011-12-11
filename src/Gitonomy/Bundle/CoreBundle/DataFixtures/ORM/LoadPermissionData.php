<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Permission;

/**
 * Loads the fixtures for user object.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadPermissionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Returns a verbose-less array with plan of userRole creation.
     *
     * @return array An array where is element is ('user-XXX', 'role-XXX', ?'project-XXX')
     */
    protected function getData()
    {
        return array(
            // Parent permissions
            array('User admin',    'USER_ADMIN',    'permission-useradmin',    false, null),
            array('Project admin', 'PROJECT_ADMIN', 'permission-projectadmin', false, null),
            array('Role admin',    'ROLE_ADMIN',    'permission-roleadmin',    false, null),
            // User
            array('User create', 'USER_CREATE', 'permission-usercreate', true, 'permission-useradmin'),
            array('User edit',   'USER_EDIT',   'permission-useredit',   true, 'permission-useradmin'),
            array('User delete', 'USER_DELETE', 'permission-userdelete', true, 'permission-useradmin'),
            // Project
            array('Project contribute', 'PROJECT_CONTRIBUTE', 'permission-projectcontribute', false, 'permission-projectadmin'),
            array('Project commit',     'PROJECT_COMMIT',     'permission-projectcommit',     false, 'permission-projectadmin'),
            array('Project create',     'PROJECT_CREATE',     'permission-projectcreate',     true,  'permission-projectadmin'),
            array('Project edit',       'PROJECT_EDIT',       'permission-projectedit',       true,  'permission-projectadmin'),
            array('Project delete',     'PROJECT_DELETE',     'permission-projectdelete',     true,  'permission-projectadmin'),
            // Role
            array('Role create', 'ROLE_CREATE', 'permission-rolecreate', true, 'permission-roleadmin'),
            array('Role edit',   'ROLE_EDIT',   'permission-roleedit',   true, 'permission-roleadmin'),
            array('Role delete', 'ROLE_DELETE', 'permission-roledelete', true, 'permission-roleadmin'),
        );
    }

    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        foreach ($this->getData() as $row) {
            list($name, $permission, $reference, $isGlobal, $parent)  = $row;

            $userRole = $this->createPermission($name, $permission, $reference, $isGlobal, $parent);

            $manager->persist($userRole);
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 20;
    }

    protected function createPermission($name, $permission, $reference, $isGlobal, $parent)
    {
        $object = new Permission();
        $object->setName($name);
        $object->setPermission($permission);
        $object->setIsGlobal($isGlobal);
        if (null !== $parent) {
            $object->setParent($this->getReference($parent));
        }
        $this->setReference($reference, $object);

        return $object;
    }

}
