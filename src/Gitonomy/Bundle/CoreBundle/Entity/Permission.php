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

namespace Gitonomy\Bundle\CoreBundle\Entity;

class Permission
{
    protected $id;
    protected $name;
    protected $isGlobal;

    public function __construct($name, $isGlobal = false)
    {
        $this->name     = $name;
        $this->isGlobal = $isGlobal;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isGlobal()
    {
        return $this->isGlobal;
    }

    public function setGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;
    }
}
