<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Repository;

/**
 * Loads fixtures for repositories.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadRepositoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $foobar = $manager->merge($this->getReference('project-foobar'));
        $barbaz = $manager->merge($this->getReference('project-barbaz'));
        $alice  = $manager->merge($this->getReference('user-alice'));
        $bob    = $manager->merge($this->getReference('user-bob'));

        $foobarMain = new Repository();
        $foobarMain->setProject($foobar);
        $manager->persist($foobarMain);

        $foobarAlice = new Repository();
        $foobarAlice->setProject($foobar);
        $foobarAlice->setOwner($alice);
        $manager->persist($foobarAlice);

        $foobarBob = new Repository();
        $foobarBob->setProject($foobar);
        $foobarBob->setOwner($bob);
        $manager->persist($foobarBob);

        $barbazMain = new Repository();
        $barbazMain->setProject($barbaz);
        $manager->persist($barbazMain);

        $barbazAlice = new Repository();
        $barbazAlice->setProject($barbaz);
        $barbazAlice->setOwner($alice);
        $manager->persist($barbazAlice);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 41;
    }
}
