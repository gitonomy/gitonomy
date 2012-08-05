<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $foobar = new Project('Foobar', 'foobar');
        $foobar->setRepositorySize(256);
        $manager->persist($foobar);
        $this->setReference('project-foobar', $foobar);

        $barbaz = new Project('Barbaz', 'barbaz');
        $barbaz->setRepositorySize(352);
        $manager->persist($barbaz);
        $this->setReference('project-barbaz', $barbaz);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 1;
    }
}
