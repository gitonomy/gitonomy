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

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Load;

use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixture;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadRoleData extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $adminPerm         = $manager->merge($this->getReference('permission-ROLE_ADMIN'));
        $projectCreatePerm = $manager->merge($this->getReference('permission-ROLE_PROJECT_CREATE'));
        $projectReadPerm   = $manager->merge($this->getReference('permission-PROJECT_READ'));
        $projectAdminPerm  = $manager->merge($this->getReference('permission-PROJECT_ADMIN'));

        $admin = new Role('Administrator', 'admin', 'Master of the application', true);
        $admin->addPermission($adminPerm);
        $admin->addPermission($projectCreatePerm);
        $manager->persist($admin);
        $this->setReference('role-admin', $admin);

        $projectCreator = new Role('Project creator', 'project-creator', 'Capable to create projects', true);
        $projectCreator->addPermission($projectCreatePerm);
        $manager->persist($projectCreator);
        $this->setReference('role-project-creator', $projectCreator);

        $leadDev = new Role('Lead developer', 'lead-dev', 'Merge leader', false);
        $leadDev->addPermission($projectReadPerm);
        $leadDev->addPermission($projectAdminPerm);
        $manager->persist($leadDev);
        $this->setReference('role-lead-developer', $leadDev);

        $developer = new Role('Developer', 'dev', 'No admin access to repositories', false);
        $developer->addPermission($projectCreatePerm);
        $developer->addPermission($projectReadPerm);
        $manager->persist($developer);
        $this->setReference('role-developer', $developer);

        $visitor = new Role('Visitor', 'visitor', 'Read-only viewers', false);
        $visitor->addPermission($projectReadPerm);
        $manager->persist($visitor);
        $this->setReference('role-visitor', $visitor);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 2; // permission
    }
}
