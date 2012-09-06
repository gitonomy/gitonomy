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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration of the Gitonomy core bundle.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gitonomyfrontend');

        $rootNode
            ->children()
                ->scalarNode('project_name')->cannotBeEmpty()->end()
                ->scalarNode('project_baseline')->cannotBeEmpty()->end()
                ->booleanNode('open_registration')->defaultTrue()->end()
                ->scalarNode('ssh_access')->cannotBeEmpty()->end()
                ->arrayNode('allowed_locales')
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('mailer')
                    ->children()
                        ->scalarNode('from_name')->cannotBeEmpty()->end()
                        ->scalarNode('from_email')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
