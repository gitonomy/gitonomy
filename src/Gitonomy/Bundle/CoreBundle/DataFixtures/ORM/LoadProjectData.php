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
class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $foobar = new Project();
        $foobar->setName('Foobar');
        $foobar->setSlug('foobar');
        $manager->persist($foobar);
        $this->setReference('project-foobar', $foobar);

        $barbaz = new Project();
        $barbaz->setName('Barbaz');
        $barbaz->setSlug('barbaz');
        $manager->persist($barbaz);
        $this->setReference('project-barbaz', $barbaz);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 100;
    }
}
