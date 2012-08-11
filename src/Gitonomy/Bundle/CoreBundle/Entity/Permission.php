<?php

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
