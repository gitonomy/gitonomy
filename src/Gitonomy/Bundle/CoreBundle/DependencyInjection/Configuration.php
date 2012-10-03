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

namespace Gitonomy\Bundle\CoreBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('gitonomy_core');

        $rootNode
            ->children()
                ->scalarNode('repository_path')->cannotBeEmpty()->end()
                ->booleanNode('enable_profiler')->defaultFalse()->end()
                ->scalarNode('async_storage')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifNotInArray(array('direct', 'mysql'))
                        ->thenInvalid('async_storage availables: direct, mysql')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
