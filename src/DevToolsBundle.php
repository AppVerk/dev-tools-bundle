<?php

declare(strict_types = 1);

namespace DevTools;

use DevTools\DependencyInjection\Compiler\AccessControlHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DevToolsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AccessControlHandlerPass());
    }
}
