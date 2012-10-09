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

namespace Gitonomy\Component\Requirements;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Requirement
{
    protected $isValid;
    protected $requirement;

    public function __construct($isValid, $requirement)
    {
        $this->isValid = (bool) $isValid;
        $this->requirement = (string) $requirement;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function getRequirement()
    {
        return $this->requirement;
    }
}
