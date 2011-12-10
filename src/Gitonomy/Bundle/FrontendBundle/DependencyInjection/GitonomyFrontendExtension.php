<?php

namespace Gitonomy\Bundle\FrontendBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Container extension for Gitonomy frontend.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class GitonomyFrontendExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('gitonomy_frontend.user.open_registration', $config['open_registration']);
        $container->setParameter('gitonomy_frontend.project.name', $config['project_name']);
        $container->setParameter('gitonomy_frontend.ssh_access', $config['ssh_access']);
    }
}
