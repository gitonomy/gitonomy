<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class GitonomyStep implements StepInterface
{
    public function getStatus(array $parameters)
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

    public function getTemplate()
    {
        return 'GitonomyDistributionBundle:Configuration:step_gitonomy.html.twig';
    }

    public function getForm()
    {
        return 'installation_step_gitonomy';
    }
}
