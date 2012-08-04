<?php

namespace Gitonomy\Bundle\DistributionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Gitonomy\Bundle\DistributionBundle\Configurator\Step\MailerStep;
use Gitonomy\Bundle\DistributionBundle\Configurator\Step\GitonomyStep;

class GitonomyDistributionBundle extends Bundle
{
    public function boot()
    {
        $configurator = $this->container->get('sensio.distribution.webconfigurator');
        $configurator->addStep(new MailerStep($configurator->getParameters()));
        $configurator->addStep(new GitonomyStep($configurator->getParameters()));
    }
}
