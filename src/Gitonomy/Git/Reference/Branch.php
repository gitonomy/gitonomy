<?php

namespace Gitonomy\Git\Reference;

use Gitonomy\Git\Reference;

/**
 * Representation of a branch reference.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Branch extends Reference
{
    /**
     * Returns the name of the branch.
     *
     * @return string
     */
    public function getName()
    {
        if (!preg_match('#^refs/heads/(.*)$#', $this->fullname, $vars)) {
            throw new \RuntimeException(sprintf('Cannot extract branch name from "%s"', $this->fullname));
        }

        return $vars[1];
    }
}
