<?php

namespace Gitonomy\Bundle\DistributionBundle\Configurator\Step;

use Sensio\Bundle\DistributionBundle\Configurator\Step\StepInterface;

class MailerStep implements StepInterface
{
    public $host;
    public $transport;
    public $user;
    public $password;
    public $encryption;
    public $authMode;

    public function __construct(array $parameters)
    {
        $this->host       = isset($parameters['mailer_host'])       ? $parameters['mailer_host']       : null;
        $this->transport  = isset($parameters['mailer_transport'])  ? $parameters['mailer_transport']  : null;
        $this->user       = isset($parameters['mailer_user'])       ? $parameters['mailer_user']       : null;
        $this->password   = isset($parameters['mailer_password'])   ? $parameters['mailer_password']   : null;
        $this->encryption = isset($parameters['mailer_encryption']) ? $parameters['mailer_encryption'] : null;
        $this->authMode   = isset($parameters['mailer_auth_mode'])  ? $parameters['mailer_auth_mode']  : null;
    }

    public function getFormType()
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
            'mailer_host'        => $data->host,
            'mailer_transport'   => $data->transport,
            'mailer_user'        => $data->user,
            'mailer_password'    => $data->password,
            'mailer_auth_mode'   => $data->authMode,
            'mailer_encryption'  => $data->encryption,
        );
    }
}
