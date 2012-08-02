<?php

namespace Gitonomy\Bundle\DistributionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Gitonomy\Bundle\DistributionBundle\Configurator\Step\MailerStep;

class GitonomyDistributionBundle extends Bundle
{
    public function boot()
    {
        $configurator = $this->container->get('sensio.distribution.webconfigurator');
        $configurator->addStep(new MailerStep($configurator->getParameters()));
    }
}
