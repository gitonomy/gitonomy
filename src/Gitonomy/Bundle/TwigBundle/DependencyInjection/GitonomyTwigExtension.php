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

namespace Gitonomy\Bundle\TwigBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class GitonomyTwigExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        //$loader->load('profiler.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['profiler']) {
            $loader->load('profiler.xml');
        }

        if ($config['twig_extension']['enabled']) {
            $loader->load('routing.xml');
            $loader->load('twig.xml');

            $container->setParameter('gitonomy_twig.themes', $config['twig_extension']['themes']);
            $container->setParameter('gitonomy_twig.url_generator.routes_names', $config['twig_extension']['routes_names']);
            $container->setParameter('gitonomy_twig.url_generator.repository_key', $config['twig_extension']['repository_key']);
        }
    }
}
