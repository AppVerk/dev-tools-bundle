<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection;

use DevTools\Doctrine\MySql\Event\DBALSchemaEventSubscriber;
use DevTools\Messenger\CommandBus;
use DevTools\Messenger\EventBus;
use DevTools\Messenger\QueryBus;
use MyCLabs\Enum\Enum;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DevToolsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->configureDoctrine($config, $container);
        $this->configureBusses($config, $container);
        $this->configureApi($config, $container);
    }

    private function configureDoctrine(array $config, ContainerBuilder $container): void
    {
        if (!$config['doctrine']['location_types']) {
            $container->removeDefinition(DBALSchemaEventSubscriber::class);
        }

        if (!empty($config['doctrine']['enum_types'])) {
            if (!class_exists('Acelaya\Doctrine\Type\PhpEnumType')) {
                throw new \LogicException(
                    'Unable to process enum types because package "acelaya/doctrine-enum-type" is missing.'
                );
            }

            foreach ($config['doctrine']['enum_types'] as $item) {
                if (!is_subclass_of($item['class'], Enum::class)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Class "%s" should be instance of "MyCLabs\Enum\Enum".',
                        $item['class']
                    ));
                }
            }

            $container->setParameter('dev_tools.doctrine.enum_types.config', $config['doctrine']['enum_types']);
        }
    }

    private function configureBusses(array $config, ContainerBuilder $container): void
    {
        if ($config['event_bus']['enabled']) {
            $container->setDefinition(
                EventBus::class,
                new Definition(EventBus::class, [new Reference($config['event_bus']['name'])])
            );
        }

        if ($config['command_bus']['enabled']) {
            $container->setDefinition(
                CommandBus::class,
                new Definition(CommandBus::class, [new Reference($config['command_bus']['name'])])
            );
        }

        if ($config['query_bus']['enabled']) {
            $container->setDefinition(
                QueryBus::class,
                new Definition(QueryBus::class, [new Reference($config['query_bus']['name'])])
            );
        }
    }

    private function configureApi(array $config, ContainerBuilder $container): void
    {
        if (!$config['api']['fos_rest']) {
            $container->removeDefinition(CommandQueryParamConverter::class);
            $container->removeDefinition(SymfonySerializerAdapter::class);
        }
    }
}
