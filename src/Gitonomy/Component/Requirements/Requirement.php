<?php

namespace Gitonomy\Component\Requirements;

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
