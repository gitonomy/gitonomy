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

use Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\UserFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * Loads the fixtures for user object.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadUserData extends UserFixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $adminRole = $this->getReference('role-admin');
        $projectCreatorRole = $this->getReference('role-project-creator');

        $admin = new User('admin', 'Admin', 'Europe/Paris');
        $admin->createEmail('admin@example.org', true);
        $admin->addGlobalRole($adminRole);
        $admin->addGlobalRole($projectCreatorRole);
        $this->setPassword($admin, 'admin');

        $manager->persist($admin);
        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 3; // role
    }
}
