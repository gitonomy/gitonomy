<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class SecretStep implements StepInterface
{
    public function getStatus()
    {
        return self::STATUS_COMPLETE;
    }

    public function getSlug()
    {
        return 'secret';
    }

    public function getName()
    {
        return 'Secret';
    }
}
