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

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadProjectData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    protected $container;

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $foobar = new Project('Foobar', 'foobar');
        $foobar->setRepositorySize(256);
        $manager->persist($foobar);
        $this->setReference('project-foobar', $foobar);
        $this->dispatch($foobar);

        $empty = new Project('Empty', 'empty');
        $empty->setRepositorySize(256);
        $manager->persist($empty);
        $this->setReference('project-empty', $empty);
        $this->dispatch($empty);

        $barbaz = new Project('Barbaz', 'barbaz');
        $barbaz->setRepositorySize(352);
        $manager->persist($barbaz);
        $this->setReference('project-barbaz', $barbaz);
        $this->dispatch($barbaz);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function dispatch(Project $project)
    {
        $this->container
            ->get('event_dispatcher')
            ->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectEvent($project))
        ;
    }
}
