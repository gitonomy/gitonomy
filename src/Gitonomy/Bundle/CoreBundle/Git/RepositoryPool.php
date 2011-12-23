<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\EventListener\Event\ProjectCreateEvent;
use Gitonomy\Git;

/**
 * Repository pool, containing all Git repositories.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class RepositoryPool
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

    public function onProjectCreate(ProjectCreateEvent $event)
    {
        $this->create($event->getProject());
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
        return new Git\Repository($this->getPath($project));
    }

    /**
     * Creates a new Git repository.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\Project $project A
     * project model instance
     */
    protected function create(Project $project)
    {
        $path = $this->getPath($project);

        if (is_dir($path)) {
            throw new \RuntimeException(sprintf('The folder "%s" already exists', $path));
        }

        Git\Admin::init($path);
    }

    /**
     * Computes the repository path for a given project.
     */
    protected function getPath(Project $project)
    {
        return $this->repositoryPath.'/'.$project->getSlug().'.git';
    }
}
