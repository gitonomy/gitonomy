<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Repository;
use Gitonomy\Bundle\CoreBundle\Git\SystemInterface as GitSystem;

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
     * Git system command.
     *
     * @var Gitonomy\Bundle\CoreBundle\Git\SystemInterface
     */
    protected $gitSystem;

    /**
     * Constructor.
     *
     * @param Symfony\Bundle\DoctrineBundle\Registry $doctrine Doctrine registry
     * @param Gitonomy\Bundle\CoreBundle\Git\SystemInterface $gitSystem The git system command
     * @param string $repositoryPath Path to the repository root folder
     */
    public function __construct(GitSystem $gitSystem,  $repositoryPath)
    {
        $this->gitSystem      = $gitSystem;
        $this->repositoryPath = $repositoryPath;
    }

    public function create(Repository $repository)
    {
        $path = $this->getPath($repository);
        $this->gitSystem->createRepository($path);
    }

    /**
     * Handles a Git command in a repository.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\Repository $repository Repository to work on
     * @param string $command Command to execute
     */
    public function command(Repository $repository, $command)
    {
        $path = $this->getPath($repository);

        $this->gitSystem->executeShell($command, $path);

    }

    /**
     * Computes the repository path for a given repository.
     * @param type $namespace
     * @param type $name
     * @return type
     */
    protected function getPath(Repository $repository)
    {
        return $this->repositoryPath.'/'.$namespace.'/'.$name.'.git';
    }
}
