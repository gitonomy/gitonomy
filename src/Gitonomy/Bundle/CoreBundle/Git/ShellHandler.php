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
        $project->getRepository()->shell($command, $env);
    }
}
