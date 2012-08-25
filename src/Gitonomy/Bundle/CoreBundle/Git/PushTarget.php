<?php

namespace Gitonomy\Bundle\CoreBundle\Git;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

class PushTarget
{
    protected $project;
    protected $reference;

    public function __construct(Project $project, $reference)
    {
        $this->project = $project;
        $this->reference = $reference;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getReference()
    {
        return $this->reference;
    }
}
