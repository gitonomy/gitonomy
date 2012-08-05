<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;

/**
 * Loads sample user SSH keys.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadUserSshKeyData extends AbstractFixture implements OrderedFixtureInterface
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
        return 4;
    }
}
