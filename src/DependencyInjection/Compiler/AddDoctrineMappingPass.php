<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddDoctrineMappingPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('dev_tools.doctrine.enum_types.config')) {
            return;
        }

        $enumConfig = $container->getParameter('dev_tools.doctrine.enum_types.config');
        $enumMapping = [];

        foreach ($enumConfig as $item) {
            foreach ($item['connections'] as $connection) {
                $connectionName = sprintf('doctrine.dbal.%s_connection', $connection);

                if (!isset($mapping[$connectionName])) {
                    $mapping[$connectionName] = [];
                }

                $enumMapping[$connectionName][$item['name']] = $item['mapping_type'];
            }
        }

        foreach ($container->getParameter('doctrine.connections') as $connectionName) {
            if (!isset($enumMapping[$connectionName])) {
                continue;
            }

            $definition = $container->getDefinition($connectionName);

            $oldMapping = $definition->getArgument(3);
            $newMapping = array_merge($oldMapping, $enumMapping[$connectionName]);

            $definition->replaceArgument(3, $newMapping);
        }
    }
}
