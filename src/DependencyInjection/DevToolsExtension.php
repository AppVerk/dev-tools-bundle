<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection;

use DevTools\FosRest\ErrorHandler\ErrorRenderer;
use DevTools\FosRest\Serializer\FlattenExceptionNormalizer;
use DevTools\Messenger\CommandBus;
use DevTools\Messenger\CommandBusScheduler;
use DevTools\Messenger\EventBus;
use DevTools\Messenger\Middleware\StreamWorkflowMiddleware;
use DevTools\Messenger\QueryBus;
use DevTools\Messenger\StreamWorkflow\Lock;
use DevTools\Messenger\StreamWorkflow\StreamWorkflow;
use DevTools\UnitTest\Fixtures\AggregateRootProcessor;
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
        $this->configureCommandBus($config, $container);
        $this->configureEventBus($config, $container);
        $this->configureQueryBus($config, $container);
        $this->configureStreamWorkflow($config, $container);
        $this->configureApi($config, $container);
        $this->configureTests($config, $container);
    }

    private function configureDoctrine(array $config, ContainerBuilder $container): void
    {
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

    private function configureApi(array $config, ContainerBuilder $container): void
    {
        if (!$config['api']['fos_rest']) {
            $container->removeDefinition('DevTools\\FosRest\\ParamConverter\\CommandQueryParamConverter');
            $container->removeDefinition('DevTools\\FosRest\\Serializer\\SymfonySerializerAdapter');
            $container->removeDefinition(ErrorRenderer::class);
            $container->removeDefinition(FlattenExceptionNormalizer::class);
            $container->removeDefinition('DevTools\\FosRest\\EventListener\\ResponseStatusCodeListener');
        }
    }

    private function configureTests(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('dev_tools.tests.config', $config['tests']);

        if (empty($config['tests']['register_helpers'])) {
            $container->removeDefinition(AggregateRootProcessor::class);

            return;
        }

        if ($container->hasDefinition(CommandBus::class)) {
            $definition = $container->getDefinition(CommandBus::class);
            $definition->setClass(\DevTools\UnitTest\Mock\CommandBus::class);
        }
    }

    private function configureStreamWorkflow(array $config, ContainerBuilder $container): void
    {
        $workflowConfig = $config['stream_workflow'];

        if (!$workflowConfig['enabled']) {
            return;
        }

        if (isset($workflowConfig['storage'], $workflowConfig['lock_factory'])) {
            $container->setDefinition(
                'dev_tools.stream_workflow.lock',
                new Definition(Lock::class, [new Reference($workflowConfig['lock_factory'])])
            );

            $container->setDefinition(
                'dev_tools.stream_workflow',
                new Definition(
                    StreamWorkflow::class,
                    [new Reference($workflowConfig['storage']), new Reference('dev_tools.stream_workflow.lock')]
                )
            );

            $container->setDefinition(
                'dev_tools.stream_workflow.middleware',
                new Definition(StreamWorkflowMiddleware::class, [new Reference('dev_tools.stream_workflow')])
            );
        }
    }

    private function configureCommandBus(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('dev_tools.command_bus.config', $config['command_bus']);

        if (!$config['command_bus']['enabled']) {
            return;
        }

        $container->setDefinition(
            CommandBus::class,
            new Definition(
                CommandBus::class,
                [new Reference($config['command_bus']['name']), $config['command_bus']['default_transport']]
            )
        );

        if ($config['command_bus']['scheduler']) {
            $container->setDefinition(
                CommandBusScheduler::class,
                new Definition(CommandBusScheduler::class, [new Reference($config['command_bus']['name'])])
            );
        }
    }

    private function configureEventBus(array $config, ContainerBuilder $container): void
    {
        if ($config['event_bus']['enabled']) {
            $container->setDefinition(
                EventBus::class,
                new Definition(EventBus::class, [new Reference($config['event_bus']['name'])])
            );
        }
    }

    private function configureQueryBus(array $config, ContainerBuilder $container): void
    {
        if ($config['query_bus']['enabled']) {
            $container->setDefinition(
                QueryBus::class,
                new Definition(QueryBus::class, [new Reference($config['query_bus']['name'])])
            );
        }
    }
}
