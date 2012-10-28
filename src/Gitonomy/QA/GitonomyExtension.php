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
        $container->setParameter('gitonomy.browser.server_url', $config['server_url']);
        $container->setParameter('gitonomy.browser.base_url', $config['base_url']);
        $container->setParameter('gitonomy.kernel_factory.app_dir', $config['app_dir']);
        $container->setParameter('gitonomy.browser.browser', $config['browser']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('gitonomy.xml');
    }

    function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('server_url')->isRequired()->end()
                ->scalarNode('base_url')->isRequired()->end()
                ->scalarNode('app_dir')->isRequired()->end()
                ->scalarNode('browser')->defaultValue('firefox')->end()
            ->end()
        ;
    }

    function getCompilerPasses()
    {
        return array();
    }
}
