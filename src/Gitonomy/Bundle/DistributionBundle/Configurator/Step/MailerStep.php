<?php

namespace Gitonomy\Bundle\DistributionBundle\Configurator\Step;

use Sensio\Bundle\DistributionBundle\Configurator\Step\StepInterface;

class MailerStep implements StepInterface
{
    public $host;
    public $transport;
    public $user;
    public $password;

    function __construct(array $parameters)
    {
        $this->host      = isset($parameters['mailer_host']) ? $parameters['mailer_host'] : null;
        $this->transport = isset($parameters['mailer_transport']) ? $parameters['mailer_transport'] : null;
        $this->user      = isset($parameters['mailer_user']) ? $parameters['mailer_user'] : null;
        $this->password  = isset($parameters['mailer_password']) ? $parameters['mailer_password'] : null;
    }

    function getFormType()
    {
        return 'configurator_step_mailer';
    }

    public function checkRequirements()
    {
        return array();
    }

    public function checkOptionalSettings()
    {
        return array();
    }

    public function getTemplate()
    {
        return 'GitonomyDistributionBundle:Configurator:step/mailer.html.twig';
    }

    public function update(StepInterface $data)
    {
        return array(
            'mailer_transport' => 'smtp',
            'mailer_host'      => 'localhost',
            'mailer_user'      => null,
            'mailer_password'  => null,
        );
    }
}
