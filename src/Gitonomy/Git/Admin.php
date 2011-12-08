<?php

namespace Gitonomy\Git;

/**
 * Administration class for Git repositories.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Admin
{
    /**
     * Initializes a repository and returns the instance. If the repository
     * already exists, this command is safe and does nothing.
     *
     * @param string $path Path to the repository
     *
     * @return Gitonomy\Git\Repository
     */
    static public function init($path)
    {
        system(sprintf('git init -q --bare %s', $path));

        return new Repository($path);
    }
}
