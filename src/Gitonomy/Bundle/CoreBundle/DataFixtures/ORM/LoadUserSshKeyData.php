<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;

/**
 * Loads sample user SSH keys.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoaderUserSshKeyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $alice = $manager->merge($this->getReference('user-alice'));
        $aliceKey = new UserSshKey();
        $aliceKey->setUser($alice);
        $aliceKey->setContent('alice-key');
        $manager->persist($aliceKey);

        $bob = $manager->merge($this->getReference('user-bob'));
        $bobKeyInstalled = new UserSshKey();
        $bobKeyInstalled->setUser($bob);
        $bobKeyInstalled->setContent('bob-key-installed');
        $bobKeyInstalled->setIsInstalled(true);
        $manager->persist($bobKeyInstalled);
        $bobKeyNotInstalled = new UserSshKey();
        $bobKeyNotInstalled->setUser($bob);
        $bobKeyNotInstalled->setContent('bob-key-not-installed');
        $manager->persist($bobKeyNotInstalled);

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
