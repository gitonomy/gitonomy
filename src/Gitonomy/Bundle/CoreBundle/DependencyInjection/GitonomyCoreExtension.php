<?php

namespace Gitonomy\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Container extension for Gitonomy.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class GitonomyCoreExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('git.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Path to repositories
        $container->setParameter('gitonomy_core.git.repository_path', $config['repository_path']);
    }
}
