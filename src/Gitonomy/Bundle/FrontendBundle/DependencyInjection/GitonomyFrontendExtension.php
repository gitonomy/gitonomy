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
        $loader->load('twig.xml');
        $loader->load('test.xml');


        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $openRegistration = $container->hasParameter('gitonomy.open_registration') ?
            $container->getParameter('gitonomy.open_registration')
            :
            $config['open_registration']
        ;
        $container->setParameter('gitonomy_frontend.user.open_registration', $openRegistration);

        $projectName = $container->hasParameter('gitonomy.project_name') ?
            $container->getParameter('gitonomy.project_name')
            :
            $config['project_name']
        ;
        $container->setParameter('gitonomy_frontend.project.name', $projectName);

        $container->setParameter('gitonomy_frontend.ssh_access', $config['ssh_access']);
    }
}
