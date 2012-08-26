<?php

namespace Gitonomy\Component\Requirements;

class RequirementCollection
{
    protected $requirements;

    public function __construct(array $requirements)
    {
        $this->requirements = $requirements;
    }

    public function isValid()
    {
        foreach ($this->requirements as $requirement) {
            if (!$requirement->isValid()) {
                return false;
            }
        }

        return true;
    }

    public function addRequirement($isValid, $requirement)
    {
        $this->requirements[] = new Requirement($isValid, $requirement);
    }

    public function getErrors()
    {
        return array_filter($this->requirements, function ($requirement) {
            return !$requirement->isValid();
        });
    }
}
