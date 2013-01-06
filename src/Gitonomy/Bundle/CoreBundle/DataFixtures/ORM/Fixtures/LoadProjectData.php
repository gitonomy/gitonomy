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
use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadProjectData extends Fixture
{
    protected $container;

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $foobar = new Project('Foobar', 'foobar');
        $foobar->setRepositorySize(256);
        $foobar->setDefaultBranch('new-feature');
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

        $secret = new Project('Secret', 'secret');
        $secret->setRepositorySize(564);
        $manager->persist($secret);
        $this->setReference('project-secret', $secret);
        $this->dispatch($secret);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 3; // role
    }

    protected function dispatch(Project $project)
    {
        $this->container
            ->get('gitonomy_core.event_dispatcher')
            ->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectEvent($project))
        ;
    }
}
