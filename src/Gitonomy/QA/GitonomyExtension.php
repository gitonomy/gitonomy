<?php

namespace Gitonomy\QA;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

use Behat\Behat\Extension\ExtensionInterface;

class GitonomyExtension implements ExtensionInterface
{
    function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter('gitonomy.kernel_factory.app_dir', $config['app_dir']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('gitonomy.xml');
    }

    function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('app_dir')->isRequired()->end()
            ->end()
        ;
    }

    function getCompilerPasses()
    {
        return array();
    }
}
