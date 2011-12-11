<?php

namespace Gitonomy\Bundle\FrontendBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Gitonomy\Bundle\FrontendBundle\DependencyInjection\Compiler\SecurityTwigPass;

/**
 * Bundle for Gitonomy frontend.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class GitonomyFrontendBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SecurityTwigPass());
    }
}
