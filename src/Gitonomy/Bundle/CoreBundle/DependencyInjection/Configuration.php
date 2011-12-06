<?php

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
                ->booleanNode('open_registration')->defaultTrue()->end()
                ->scalarNode('repository_path')->cannotBeEmpty()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
