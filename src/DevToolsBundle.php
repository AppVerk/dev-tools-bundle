<?php

declare(strict_types = 1);

namespace DevTools;

use Acelaya\Doctrine\Type\PhpEnumType;
use DevTools\DependencyInjection\Compiler\AccessControlHandlerPass;
use DevTools\DependencyInjection\Compiler\AddDoctrineMappingPass;
use DevTools\DependencyInjection\Compiler\RegisterTestHelpersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DevToolsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AccessControlHandlerPass());
        $container->addCompilerPass(new AddDoctrineMappingPass());
        $container->addCompilerPass(new RegisterTestHelpersPass());
    }

    public function boot(): void
    {
        parent::boot();

        if ($this->container->hasParameter('dev_tools.doctrine.enum_types.config')) {
            foreach ((array) $this->container->getParameter('dev_tools.doctrine.enum_types.config') as $item) {
                if (PhpEnumType::hasType($item['name'])) {
                    continue;
                }

                PhpEnumType::registerEnumType($item['name'], $item['class']);
            }
        }
    }
}
