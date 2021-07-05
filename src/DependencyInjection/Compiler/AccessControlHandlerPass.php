<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection\Compiler;

use DevTools\Messenger\ObjectMapper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AccessControlHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $authorizationChecker = new Reference(AuthorizationCheckerInterface::class);
        $objectMapper = new Reference(ObjectMapper::class);

        foreach ($container->findTaggedServiceIds('messenger.message_handler') as $id => $tags) {
            $definition = $container->getDefinition($id);

            if (method_exists($definition->getClass(), 'setAuthorizationChecker')) {
                $definition->addMethodCall('setAuthorizationChecker', [$authorizationChecker]);
            }

            if (method_exists($definition->getClass(), 'setObjectMapper')) {
                $definition->addMethodCall('setObjectMapper', [$objectMapper]);
            }
        }
    }
}
