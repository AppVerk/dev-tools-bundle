<?php

declare(strict_types = 1);

namespace DevTools;

use DevTools\DependencyInjection\Compiler\AccessControlHandlerPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DevToolsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AccessControlHandlerPass());

        $this->addRegisterMappingsPass($container);
        $this->registerJWTMappingsPass($container);
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        $currentDirectory = realpath(__DIR__);
        $directories = [];
        $aliasMap = [];
    }

    private function registerJWTMappingsPass(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        $class = new ReflectionClass(GesdinetJWTRefreshTokenBundle::class);

        if (isset($bundles[$class->getShortName()])) {
            return;
        }

        $namespace = $class->getNamespaceName() . '\Entity';
        $path = dirname($class->getFileName());

        $mappings = [
            $path . '/Resources/config/orm/doctrine-orm' => $namespace,
            $path . '/Resources/config/orm/doctrine-superclass' => $namespace,
        ];

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
    }
}
