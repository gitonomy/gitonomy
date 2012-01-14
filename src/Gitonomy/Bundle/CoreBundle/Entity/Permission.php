<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Permission extends Base\BasePermission
{
    public function __construct()
    {
        $this->isGlobal = false;
        parent::__construct();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function hasParent()
    {
        return ($this->parent instanceof Permission);
    }
}
