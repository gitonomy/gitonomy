<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

/**
 * Interface for a Git system.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
interface SystemInterface
{
    /**
     * Creates a new Git repository in the indicated folder.
     *
     * @param string $path A path to a non-existing folder
     *
     * @throws RuntimeException If error occurs during repository creation
     */
    function createRepository($path);

    /**
     * Clones a repository.
     *
     * @param string $pathFrom The origin path
     * @param string $pathTo   The target path
     */
    function cloneRepository($pathFrom, $pathTo);

    /**
     * Executes a shell command on a Git repository.
     *
     * @param string $command A git-command to execute
     * @param string $path Path to a git repository
     */
    function executeShell($command, $path);
}
