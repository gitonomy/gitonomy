<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Permission;

/**
 * Loads the fixtures for user object.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadPermissionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Returns a verbose-less array with plan of permission creation.
     */
    protected function getData()
    {
        return array(
            'global'  => array('ROLE_ADMIN'),
            'project' => array('PROJECT_CONTRIBUTE')
        );
    }

    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $data = $this->getData();

        foreach ($data as $case => $permissions) {
            $isGlobal = $case == 'global';
            foreach ($permissions as $name) {
                $permission = new Permission();
                $permission->setName($name);
                $permission->setIsGlobal($isGlobal);
                $this->setReference('permission-'.$name, $permission);
                $manager->persist($permission);
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
}
