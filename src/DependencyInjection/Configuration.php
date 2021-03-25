<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dev_tools');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('doctrine_extensions')
                    ->defaultValue(false)
                ->end()
                ->arrayNode('event_bus')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(true)
                        ->end()
                        ->scalarNode('name')
                            ->defaultValue('event_bus')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('command_bus')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(true)
                        ->end()
                        ->scalarNode('name')
                            ->defaultValue('command_bus')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('query_bus')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(true)
                        ->end()
                        ->scalarNode('name')
                            ->defaultValue('query_bus')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
