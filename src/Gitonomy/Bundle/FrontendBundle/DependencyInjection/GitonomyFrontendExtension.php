<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

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
        $container->setParameter('gitonomy_frontend.project.baseline', $config['project_baseline']);
        $container->setParameter('gitonomy_frontend.ssh_access', $config['ssh_access']);
        $container->setParameter('gitonomy_frontend.allowed_locales', $config['allowed_locales']);
        $container->setParameter('gitonomy_frontend.mailer.from', array($config['mailer']['from_email'] => $config['mailer']['from_name']));
    }
}
