<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp;

use DevTools\Messenger\Bridge\Amqp\Routing\CashedRoutingStrategy;
use DevTools\Messenger\Bridge\Amqp\Routing\ClassBasedStrategy;
use DevTools\Messenger\Bridge\Amqp\Routing\RoutingStrategyInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class RoutingStrategyFactory
{
    public function createClassBasedStrategy(): RoutingStrategyInterface
    {
        return new CashedRoutingStrategy(new ClassBasedStrategy(
            new CamelCaseToSnakeCaseNameConverter(null, false)
        ));
    }
}
