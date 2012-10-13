<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation;

interface StepInterface
{
    const STATUS_ERROR = 0;
    const STATUS_OK    = 1;

    public function getSlug();

    public function getStatus(array $parameters);

    public function getTemplate();

    public function getForm();
}
