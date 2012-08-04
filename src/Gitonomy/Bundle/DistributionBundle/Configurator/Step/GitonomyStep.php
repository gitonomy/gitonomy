<?php

namespace Gitonomy\Bundle\DistributionBundle\Configurator\Step;

use Sensio\Bundle\DistributionBundle\Configurator\Step\StepInterface;

class GitonomyStep implements StepInterface
{
    public $project_name;
    public $project_baseline;
    public $open_registration;
    public $repository_path;

    function __construct(array $parameters)
    {
        foreach ($this as $key => $value) {
            $this->$key = isset($parameters[$key]) ? $parameters[$key] : null;
        }
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
        $result = array();
        foreach ($data as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }
}
