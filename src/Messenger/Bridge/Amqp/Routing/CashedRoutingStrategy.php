<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Routing;

class CashedRoutingStrategy implements RoutingStrategyInterface
{
    /**
     * @var string[]
     */
    private array $rotingKeyCache;

    /**
     * @var string[]
     */
    private array $classCache;

    private RoutingStrategyInterface $original;

    public function __construct(RoutingStrategyInterface $original)
    {
        $this->original = $original;
        $this->rotingKeyCache = [];
        $this->classCache = [];
    }

    public function getClass(string $routingKey, array $context): ?string
    {
        if (!isset($this->classCache[$routingKey])) {
            $class = $this->original->getClass($routingKey, $context);

            if (null === $class) {
                return null;
            }

            $this->rotingKeyCache[$class] = $routingKey;
            $this->classCache[$routingKey] = $class;
        }

        return $this->classCache[$routingKey];
    }

    public function getRoutingKey(string $class, array $context): ?string
    {
        if (!isset($this->rotingKeyCache[$class])) {
            $routingKey = $this->original->getRoutingKey($class, $context);

            if (null === $routingKey) {
                return null;
            }

            $this->rotingKeyCache[$class] = $routingKey;
            $this->classCache[$routingKey] = $class;
        }

        return $this->rotingKeyCache[$class];
    }
}
