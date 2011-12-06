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
     * Doctrine registry, containing objects.
     *
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

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
    public function __construct(Doctrine $doctrine, GitSystem $gitSystem,  $repositoryPath)
    {
        $this->gitSystem      = $gitSystem;
        $this->doctrine       = $doctrine;
        $this->repositoryPath = $repositoryPath;
    }

    /**
     * Creates a new user repository.
     *
     * @param string $user A user
     *
     * @param string $name A repository name
     */
    public function create(User $user, $name)
    {
        $namespace = $user->getUsername();

        if ($this->exists($namespace, $name)) {
            return;
        }

        $em = $this->doctrine->getEntityManager();

        $repository = new Repository();
        $repository->setOwner($user);
        $repository->setNamespace($namespace);
        $repository->setName($name);
        $em->persist($repository);

        $path = $this->getPath($namespace, $name);
        $this->gitSystem->createRepository($path);

        $em->flush();
    }

    /**
     * Tests is a repository exists
     *
     * @param string $namespace Namespace
     *
     * @param string $name Repository name
     */
    public function exists($namespace, $name)
    {
        $em = $this->doctrine->getEntityManager();

        return $em->getRepository('GitonomyCoreBundle:Repository')->exists($namespace, $name);
    }

    /**
     * Fetches a repository.
     *
     * @param string $namespace A namespace
     * @param string $name A name
     *
     * @return Gitonomy\Bundle\CoreBundle\Entity\Repository A repository object
     */
    public function find($namespace, $name)
    {
        $em = $this->doctrine->getEntityManager();

        return $em->getRepository('GitonomyCoreBundle:Repository')->findOneBy(array(
            'namespace' => $namespace,
            'name'      => $name
        ));
    }

    /**
     * Handles a Git command in a repository.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\Repository $repository Repository to work on
     * @param string $command Command to execute
     */
    public function command(Repository $repository, $command)
    {
        $repositoryPath = $this->getPath($namespace, $name);

        $this->gitSystem->executeShell($command, $repositoryPath);

    }

    /**
     * Forks a repository for a user.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\User $user A user
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\Repository $repository A repository
     */
    public function fork(User $user, Repository $repository)
    {
        $username  = $user->getUsername();
        $name      = $repository->getName();
        $namespace = $repository->getNamespace();

        $from = $this->getPath($namespace, $name);
        $to   = $this->getPath($username,  $name);

        $this->gitSystem->cloneRepository($from, $to);

        $newRepository = new Repository();
        $newRepository->setNamespace($username);
        $newRepository->setName($name);
        $newRepository->setOwner($user);
        $newRepository->setForkedFrom($repository);
        $newRepository->setDescription($repository->getDescription());

        $em = $this->doctrine->getEntityManager();
        $em->persist($newRepository);
        $em->flush();
    }

    /**
     * Computes the repository path for a given repository.
     * @param type $namespace
     * @param type $name
     * @return type
     */
    protected function getPath($namespace, $name)
    {
        return $this->repositoryPath.'/'.$namespace.'/'.$name.'.git';
    }
}
