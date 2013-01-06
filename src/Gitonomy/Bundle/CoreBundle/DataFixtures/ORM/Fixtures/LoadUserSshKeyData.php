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

use Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixture;

/**
 * Loads sample user SSH keys.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadUserSshKeyData extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $alice = $manager->merge($this->getReference('user-alice'));
        $bob = $manager->merge($this->getReference('user-bob'));

        $aliceKey = $alice->createSshKey('Laptop key', 'alice-key');
        $aliceKey->setInstalled(true);
        $manager->persist($aliceKey);

        $bobKey = $bob->createSshKey('Installed key', 'bob-key-installed');
        $bobKey->setInstalled(true);
        $manager->persist($bobKey);

        $bobKeyNotInstalled = $bob->createSshKey('Not installed key', 'bob-key-not-installed');
        $manager->persist($bobKeyNotInstalled);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 4; // user
    }
}
