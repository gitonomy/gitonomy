<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\UserRole;

/**
 * Loads the fixtures for user role object.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadUserRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $admin = new UserRole();
        $admin->setUser($this->getReference('user-admin'));
        $admin->setRole($this->getReference('role-admin'));
        $manager->persist($admin);
        $this->setReference('permission-userdelete', $admin);

        $aliceLead = new UserRole();
        $aliceLead->setUser($this->getReference('user-alice'));
        $aliceLead->setRole($this->getReference('role-leaddev'));
        $manager->persist($aliceLead);
        $this->setReference('userrole-alice-leaddev', $aliceLead);

        $bobDev = new UserRole();
        $bobDev->setUser($this->getReference('user-bob'));
        $bobDev->setRole($this->getReference('role-dev'));
        $bobDev->setProject($this->getReference('project-foobar'));
        $manager->persist($bobDev);
        $this->setReference('userrole-bob-dev-foobar', $bobDev);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 50;
    }
}
