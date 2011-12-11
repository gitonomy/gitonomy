<?php


namespace Gitonomy\Bundle\FrontendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/*
 * @author Julien DIDIER <julien@jdidier.net>
 */
class SecurityTwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('twig.extension.security')) {
            return;
        }

        $definition = $container->getDefinition('twig.extension.security');
        $definition->setClass('Gitonomy\Bundle\FrontendBundle\Twig\SecurityExtension');
        $definition->addMethodCall('setRight', array(new Reference('gitonomy_frontend.security.right')));
    }
}
