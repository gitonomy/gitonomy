<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\Permission;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadPermissionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $permissions = array(
            new Permission('ROLE_ADMIN', true),
            new Permission('PROJECT_CONTRIBUTE', false)
        );

        foreach ($permissions as $permission) {
            $manager->persist($permission);
            $this->setReference('permission-'.$permission->getName(), $permission);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
