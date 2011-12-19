<?php

namespace Gitonomy\Git\Reference;

use Gitonomy\Git\Reference;

/**
 * Representation of a remote reference.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Remote extends Reference
{
    /**
     * Returns the name of the tag.
     *
     * @return string
     */
    public function getRemoteName()
    {
        if (!preg_match('#^refs/remotes/(.*)/(.*)$#', $this->fullname, $vars)) {
            throw new \RuntimeException(sprintf('Cannot extract remote name from "%s"', $this->fullname));
        }

        return $vars[1];
    }

    /**
     * Returns the name of the tag.
     *
     * @return string
     */
    public function getRemoteReference()
    {
        if (!preg_match('#^refs/remotes/(.*)/(.*)$#', $this->fullname, $vars)) {
            throw new \RuntimeException(sprintf('Cannot extract remote reference from "%s"', $this->fullname));
        }

        return $vars[2];
    }
}
