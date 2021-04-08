<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RetrieverLocator
{
    private ContainerInterface $serviceLocator;

    public function __construct(ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function locate(MapInterface $map): RetrieverInterface
    {
        $retrieverClass = null === $map->getRetrieverClass()
            ? mb_substr(get_class($map), 0, -3) . 'Retriever'
            : $map->getRetrieverClass();

        // @phpstan-ignore-next-line
        return $this->serviceLocator->get($retrieverClass);
    }
}
