<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection\Compiler;

use DevTools\Messenger\Envelope\ForcedSenderLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConfigureMessageBusesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $commandBusConfig = (array) $container->getParameter('dev_tools.command_bus.config');

        if (!$commandBusConfig['enabled']) {
            return;
        }

        if ($container->hasDefinition('messenger.senders_locator')) {
            $baseDefinition = $container->getDefinition('messenger.senders_locator');

            $newDefinition = new Definition(ForcedSenderLocator::class, [
                new Reference(ForcedSenderLocator::class . '.inner'),
                $baseDefinition->getArgument(1),
            ]);

            $newDefinition->setDecoratedService('messenger.senders_locator');

            $container->setDefinition(ForcedSenderLocator::class, $newDefinition);
        }
    }
}
