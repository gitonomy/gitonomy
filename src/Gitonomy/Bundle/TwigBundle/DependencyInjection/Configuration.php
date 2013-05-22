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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration of the Gitonomy core bundle.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    const DEFAULT_THEME = 'GitonomyTwigBundle::default_theme.html.twig';

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gitonomy_twig');

        $rootNode
            ->children()
                ->booleanNode('profiler')->defaultFalse()->end()
                ->arrayNode('twig_extension')
                ->beforeNormalization()
                ->ifTrue()
                    ->then(function () { return array('enabled' => true); })
                ->end()
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultFalse()->end()
                    ->booleanNode('profiler')->defaultFalse()->end()
                    ->scalarNode('repository_key')->defaultValue('slug')->end()
                    ->arrayNode('routes_names')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('commit')->defaultValue('project_commit')->end()
                        ->scalarNode('reference')->defaultValue('project_reference')->end()
                    ->end()
                ->end()
                ->arrayNode('themes')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('key')
                    ->beforeNormalization()
                        ->always(function ($e) {
                            if (!in_array(self::DEFAULT_THEME, $e)) {
                                $e[] = self::DEFAULT_THEME;
                            }

                            return $e;
                        })
                    ->end()
                    ->defaultValue(array(self::DEFAULT_THEME))
                    ->prototype('scalar')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
