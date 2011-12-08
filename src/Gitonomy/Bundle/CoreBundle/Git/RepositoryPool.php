<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Gitonomy\Bundle\CoreBundle\Entity\Repository;
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

    public function create(Repository $repository)
    {
        if (is_dir($path)) {
            throw new \RuntimeException(sprintf('The folder "%s" already exists', $path));
        }

        Git\Admin::init($path);
    }

    public function getGitRepository(Repository $repository)
    {
        return new Git\Repository($this->getPath($repository));
    }

    /**
     * Computes the repository path for a given repository.
     */
    protected function getPath(Repository $repository)
    {
        $slug = $repository->getProject()->getSlug();
        if ($repository->getIsProjectRepository()) {
            return $this->repositoryPath.'/projects/'.$slug.'.git';
        } else {
            $username = $repository->getOwner()->getUsername();
            return $this->repositoryPath.'/users/'.$username.'/'.$slug.'.git';
        }
    }
}
