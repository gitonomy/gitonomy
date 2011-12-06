<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Repository;

/**
 * Loads the fixtures for repository object.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoaderRepositoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $alice = $manager->merge($this->getReference('user-alice'));
        $bob   = $manager->merge($this->getReference('user-bob'));

        $repositoryFoobarAlice = new Repository();
        $repositoryFoobarAlice->setOwner($alice);
        $repositoryFoobarAlice->setDescription('The Foo Bar project');
        $repositoryFoobarAlice->setNamespace('alice');
        $repositoryFoobarAlice->setName('foobar');
        $manager->persist($repositoryFoobarAlice);

        $repositoryBarbazAlice = new Repository();
        $repositoryBarbazAlice->setOwner($alice);
        $repositoryBarbazAlice->setDescription('The Barbaz project');
        $repositoryBarbazAlice->setNamespace('alice');
        $repositoryBarbazAlice->setName('barbaz');
        $manager->persist($repositoryBarbazAlice);

        $repositoryFoobarBob = new Repository();
        $repositoryFoobarBob->setForkedFrom($repositoryFoobarAlice);
        $repositoryFoobarBob->setOwner($bob);
        $repositoryFoobarBob->setDescription('The Foo Bar project, Bob fork');
        $repositoryFoobarBob->setNamespace('bob');
        $repositoryFoobarBob->setName('foobar');
        $manager->persist($repositoryFoobarBob);

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
