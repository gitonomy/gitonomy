<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * Loads the fixtures for project object.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadProjectData extends AbstractFixture
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $foo = new Project();
        $foo->setName('Foo');
        $manager->persist($foo);
        $this->setReference('project-foo', $foo);

        $bar = new Project();
        $bar->setName('Bar');
        $manager->persist($bar);
        $this->setReference('project-bar', $bar);

        $baz = new Project();
        $baz->setName('Baz');
        $manager->persist($baz);
        $this->setReference('project-baz', $baz);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 40;
    }
}
