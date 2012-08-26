<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class DoctrineStep implements StepInterface
{
    public function testValues(array $parameters)
    {
        return array(0, "OK");
    }

    public function getStatus(array $parameters)
    {
        list($code, $message) = $this->testValues($parameters);

        if ($code == 0) {
            return self::STATUS_SUCCESS;
        }

        return self::STATUS_ERROR;
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
