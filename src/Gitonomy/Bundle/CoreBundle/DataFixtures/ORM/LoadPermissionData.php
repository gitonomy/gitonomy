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
            'global' => array(
                'USER_ADMIN'    => array('USER_CREATE', 'USER_EDIT', 'USER_DELETE'),
                'PROJECT_ADMIN' => array('PROJECT_CREATE', 'PROJECT_EDIT', 'PROJECT_DELETE'),
                'ROLE_ADMIN'    => array('ROLE_CREATE', 'ROLE_EDIT', 'ROLE_DELETE')
            ),
            'project' => array(
                'GIT_CONTRIBUTE' => array('GIT_READ',  'GIT_WRITE', 'GIT_FORCE', 'GIT_MAIN'),
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $data = $this->getData();

        foreach ($data as $case => $rows) {
            $isGlobal = $case == 'global';
            foreach ($rows as $parentName => $children) {
                $parentPermission = $this->createPermission($parentName, $isGlobal);
                foreach ($children as $childName) {
                    $permission = $this->createPermission($childName, $isGlobal, $parentName);
                    $manager->persist($permission);
                }
                $manager->persist($parentPermission);
            }
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 200;
    }

    protected function createPermission($name, $isGlobal, $parent = null)
    {
        $object = new Permission();
        $object->setName($name);
        $object->setIsGlobal($isGlobal);

        if (null !== $parent) {
            $object->setParent($this->getReference('permission-'.$parent));
        }
        $this->setReference('permission-'.$name, $object);

        return $object;
    }

}
