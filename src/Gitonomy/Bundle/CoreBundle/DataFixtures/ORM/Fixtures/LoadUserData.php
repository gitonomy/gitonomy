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

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\UserFixture;
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
        $adminRole          = $this->getReference('role-admin');
        $projectCreatorRole = $this->getReference('role-project-creator');

        $users = array();

        $lead = new User('lead', 'Lead', 'Europe/Paris');
        $lead->addGlobalRole($projectCreatorRole);
        $lead->createEmail('lead@example.org', true);
        $this->setPassword($lead, 'lead');
        $users[] = $lead;

        $charlie = new User('charlie', 'Visitor', 'Europe/Paris');
        $charlie->createEmail('charlie@example.org');
        $this->setPassword($charlie, 'charlie');
        $users[] = $charlie;

        $alice = new User('alice', 'Alice', 'Europe/Paris');
        $alice->addGlobalRole($projectCreatorRole);
        $alice->createEmail('alice@example.org', true);
        $alice->createEmail('derpina@example.org');
        $this->setPassword($alice, 'alice');
        $users[] = $alice;

        $bob = new User('bob', 'Bob', 'Europe/Paris');
        $bob->createEmail('bob@example.org', true);
        $this->setPassword($bob, 'bob');
        $users[] = $bob;

        $inactive = new User('inactive', 'Inactive', 'Europe/Paris');
        $inactive->createEmail('inactive@example.org', true)->createActivationToken();
        $inactive->createActivationToken();
        $users[] = $inactive;

        foreach ($users as $user) {
            $manager->persist($user);
            $this->setReference('user-'.$user->getUsername(), $user);
        }

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
