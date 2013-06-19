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

namespace Gitonomy\Bundle\CoreBundle\Git;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Git\Repository;
use Gitonomy\Git\Admin;

/**
 * Repository pool, containing all Git repositories.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class RepositoryPool implements EventSubscriberInterface
{
    /**
     * Root directory of every repositories.
     *
     * @var string
     */
    protected $repositoryPath;

    /**
     * Constructor.
     *
     * @param string $repositoryPath Path to the repository root folder
     */
    public function __construct($repositoryPath)
    {
        $this->repositoryPath = $repositoryPath;
    }

    public static function getSubscribedEvents()
    {
        return array(
            GitonomyEvents::PROJECT_CREATE => array(array('onProjectCreate', 255)),
            GitonomyEvents::PROJECT_DELETE => array(array('onProjectDelete', -255)),
            GitonomyEvents::PROJECT_PUSH   => array(array('onProjectPush', -255))
        );
    }

    /**
     * Method called when a project is created
     */
    public function onProjectCreate(ProjectEvent $event)
    {
        $project = $event->getProject();

        $path = $this->getPath($project);

        Admin::init($path);

        $repository = $this->getGitRepository($project);
        $project->setRepository($repository);
        $project->setRepositorySize($repository->getSize());
    }

    /**
     * Method called when a project is deleted
     */
    public function onProjectDelete(ProjectEvent $event)
    {
        $path = $this->getPath($event->getProject());

        $flags = \FilesystemIterator::SKIP_DOTS;
        $iterator = new \RecursiveDirectoryIterator($path, $flags);
        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            if (is_file($file)) {
                unlink($file);
            } else {
                rmdir($file);
            }
        }
        rmdir($path);
    }

    public function onProjectPush(PushReferenceEvent $event)
    {
        $project = $event->getProject();
        $repository = $this->getGitRepository($project);

        $project->setRepositorySize($repository->getSize());
    }

    /**
     * Returns the Git repository associated the a model project.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\Project $project A
     * project model instance
     *
     * @return Gitonomy\Git\Repository A Git repository
     */
    public function getGitRepository(Project $project)
    {
        return new Repository($this->getPath($project));
    }

    /**
     * Computes the repository path for a given project.
     */
    protected function getPath(Project $project)
    {
        return $this->repositoryPath.'/'.$project->getSlug().'.git';
    }
}
