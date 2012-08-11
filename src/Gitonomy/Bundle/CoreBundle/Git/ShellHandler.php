<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;
use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * Handler of shell commands.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ShellHandler
{
    /**
     * Current repository pool
     *
     * @var Gitonomy\Bundle\CoreBundle\Git\RepositoryPool
     */
    protected $repositoryPool;

    /**
     * Constructor.
     */
    public function __construct(RepositoryPool $repositoryPool)
    {
        $this->repositoryPool = $repositoryPool;
    }

    /**
     * Returns the original Git command.
     */
    public function getOriginalCommand()
    {
        return isset($_SERVER['SSH_ORIGINAL_COMMAND']) ? $_SERVER['SSH_ORIGINAL_COMMAND'] : null;
    }

    /**
     * Handles the git pack.
     */
    public function handle(Project $project, $command, array $env = array())
    {
        $this->repositoryPool->getGitRepository($project)->shell($command, $env);
    }
}
