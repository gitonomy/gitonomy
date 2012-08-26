<?php

namespace Gitonomy\Bundle\DistributionBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


class AddStepToListPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('gitonomy_distribution.steps')) {
            return;
        }

        $def = $container->getDefinition('gitonomy_distribution.steps');
        $services = $container->findTaggedServiceIds('install.step');

        $arg = $def->getArgument(0);
        foreach (array_keys($services) as $service) {
            $arg[] = new Reference($service);
        }
        $def->replaceArgument(0, $arg);
    }
}
