<?php

namespace Gitonomy\Git\Reference;

use Gitonomy\Git\Reference;

/**
 * Representation of a tag reference.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Tag extends Reference
{
    /**
     * Returns the name of the tag.
     *
     * @return string
     */
    public function getName()
    {
        if (!preg_match('#^refs/tags/(.*)$#', $this->fullname, $vars)) {
            throw new \RuntimeException(sprintf('Cannot extract tag name from "%s"', $this->fullname));
        }

        return $vars[1];
    }
}
