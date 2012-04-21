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
        $aliceKey = new UserSshKey();
        $aliceKey->setUser($alice);
        $aliceKey->setTitle('Laptop key');
        $aliceKey->setContent('alice-key');
        $manager->persist($aliceKey);

        $bob = $manager->merge($this->getReference('user-bob'));
        $bobKeyInstalled = new UserSshKey();
        $bobKeyInstalled->setTitle('Installed key');
        $bobKeyInstalled->setUser($bob);
        $bobKeyInstalled->setContent('bob-key-installed');
        $bobKeyInstalled->setIsInstalled(true);
        $manager->persist($bobKeyInstalled);
        $bobKeyNotInstalled = new UserSshKey();
        $bobKeyNotInstalled->setUser($bob);
        $bobKeyNotInstalled->setTitle('Not installed key');
        $bobKeyNotInstalled->setContent('bob-key-not-installed');
        $manager->persist($bobKeyNotInstalled);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 250;
    }
}
