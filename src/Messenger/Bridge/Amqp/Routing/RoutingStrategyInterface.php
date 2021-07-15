<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Routing;

interface RoutingStrategyInterface
{
    public function getClass(string $routingKey, array $context): ?string;

    public function getRoutingKey(string $class, array $context): ?string;
}
