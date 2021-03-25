<?php

declare(strict_types = 1);

namespace DevTools\DependencyInjection;

use DevTools\Doctrine\MySql\Event\DBALSchemaEventSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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
    }

    private function configureDoctrine(array $config, ContainerBuilder $container): void
    {
        if (!$config['doctrine_extensions']) {
            $container->removeDefinition(DBALSchemaEventSubscriber::class);
        }
    }
}
