<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp;

use DevTools\Messenger\Bridge\Amqp\Routing\RoutingStrategyInterface;
use DevTools\Messenger\Bridge\Amqp\Serialization\HeadersConverter;
use DevTools\Messenger\Bridge\Amqp\Serialization\SharedTransportSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class SerializerFactory
{
    private SerializerInterface $originalSerializer;

    private RoutingStrategyInterface $routingStrategy;

    public function __construct(SerializerInterface $originalSerializer, RoutingStrategyInterface $routingStrategy)
    {
        $this->originalSerializer = $originalSerializer;
        $this->routingStrategy = $routingStrategy;
    }

    /**
     * @param string[] $headersMap
     */
    public function create(
        string $busName,
        array $routingContext = [],
        array $headersMap = []
    ): SerializerInterface {
        $headersConverter = new HeadersConverter($this->routingStrategy, $routingContext, $headersMap);

        return new SharedTransportSerializer($busName, $this->originalSerializer, $headersConverter);
    }
}
