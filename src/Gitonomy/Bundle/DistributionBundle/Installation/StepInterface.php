<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation;

interface StepInterface
{
    const STATUS_COMPLETE = 0;
    const STATUS_WARNING  = 1;
    const STATUS_ERRORS   = 2;

    public function getSlug();

    public function getStatus();
}
