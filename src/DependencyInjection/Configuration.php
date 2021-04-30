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
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('location_types')
                            ->defaultValue(false)
                        ->end()
                        ->arrayNode('enum_types')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')
                                        ->defaultValue('string')
                                    ->end()
                                    ->scalarNode('mapping_type')
                                        ->defaultValue('string')
                                    ->end()
                                    ->scalarNode('class')
                                        ->isRequired()
                                    ->end()
                                    ->arrayNode('connections')
                                        ->defaultValue(['default'])
                                        ->scalarPrototype()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
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
                ->arrayNode('api')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('fos_rest')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('tests')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('register_helpers')
                            ->defaultValue(false)
                        ->end()
                        ->scalarNode('fixtures_location')
                            ->defaultValue('%kernel.project_dir%/tests/Functional')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
