<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class DoctrineStep implements StepInterface
{
    public function getStatus()
    {
        return self::STATUS_COMPLETE;
    }

    public function getSlug()
    {
        return 'database';
    }

    public function getName()
    {
        return 'Database';
    }
}
