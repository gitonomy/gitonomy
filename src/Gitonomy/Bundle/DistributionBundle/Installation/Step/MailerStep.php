<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class MailerStep implements StepInterface
{
    public function getStatus(array $parameters)
    {
        return self::STATUS_COMPLETE;
    }

    public function getSlug()
    {
        return 'mailer';
    }

    public function getName()
    {
        return 'Mailer';
    }

    public function getTemplate()
    {
        return 'GitonomyDistributionBundle:Configuration:step_mailer.html.twig';
    }

    public function getForm()
    {
        return 'installation_step_mailer';
    }
}
