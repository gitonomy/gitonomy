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

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\Role;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $adminPerm      = $manager->merge($this->getReference('permission-ROLE_ADMIN'));
        $contributePerm = $manager->merge($this->getReference('permission-PROJECT_CONTRIBUTE'));

        $admin = new Role('Administrator', 'admin', 'Master of the application', true);
        $admin->addPermission($adminPerm);
        $manager->persist($admin);
        $this->setReference('role-admin', $admin);

        $leadDev = new Role('Lead developer', 'lead-dev', 'Merge leader', false);
        $leadDev->addPermission($contributePerm);
        $manager->persist($leadDev);
        $this->setReference('role-lead-developer', $leadDev);

        $visitor = new Role('Visitor', 'visitor', 'Read-only viewers', false);
        $visitor->addPermission($contributePerm);
        $manager->persist($visitor);
        $this->setReference('role-visitor', $visitor);

        $developer = new Role('Developer', 'dev', 'No admin access to repositories', false);
        $developer->addPermission($contributePerm);
        $manager->persist($developer);
        $this->setReference('role-developer', $developer);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 2;
    }
}
