<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class GitonomyStep implements StepInterface
{
    public function getStatus()
    {
        return self::STATUS_COMPLETE;
    }

    public function getSlug()
    {
        return 'gitonomy';
    }

    public function getName()
    {
        return 'Gitonomy';
    }
}
