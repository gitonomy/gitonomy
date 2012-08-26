<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class DoctrineStep implements StepInterface
{
    public function getStatus(array $parameters)
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

    public function getTemplate()
    {
        return 'GitonomyDistributionBundle:Configuration:step_doctrine.html.twig';
    }

    public function getForm()
    {
        return 'installation_step_doctrine';
    }
}
