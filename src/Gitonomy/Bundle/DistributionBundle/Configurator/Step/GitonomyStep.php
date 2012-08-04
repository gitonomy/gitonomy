<?php

namespace Gitonomy\Bundle\DistributionBundle\Configurator\Step;

use Sensio\Bundle\DistributionBundle\Configurator\Step\StepInterface;

class GitonomyStep implements StepInterface
{
    public $repository_path;

    function __construct(array $parameters)
    {
        $this->repository_path = isset($parameters['repository_path']) ? $parameters['repository_path'] : null;
    }

    function getFormType()
    {
        return 'configurator_step_gitonomy';
    }

    public function checkRequirements()
    {
    }

    public function checkOptionalSettings()
    {
    }

    public function getTemplate()
    {
        return 'GitonomyDistributionBundle:Configurator:step/gitonomy.html.twig';
    }

    public function update(StepInterface $data)
    {
        return array(
            'repository_path' => $this->repository_path
        );
    }
}
