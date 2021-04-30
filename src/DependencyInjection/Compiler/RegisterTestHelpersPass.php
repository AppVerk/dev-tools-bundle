<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection\Compiler;

use DevTools\UnitTest\Fixtures\TestCaseFixturesLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterTestHelpersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $config = (array) $container->getParameter('dev_tools.tests.config');

        if (empty($config['register_helpers'])) {
            return;
        }

        if ($container->hasDefinition('hautelook_alice.locator.environmentless')) {
            $definition = new Definition(TestCaseFixturesLocator::class, [
                new Reference(TestCaseFixturesLocator::class . '.inner'),
                $config['fixtures_location'],
            ]);
            $definition->setDecoratedService('hautelook_alice.locator.environmentless');

            $container->setDefinition(TestCaseFixturesLocator::class, $definition);
        }
    }
}
