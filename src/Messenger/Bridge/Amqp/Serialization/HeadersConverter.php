<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Serialization;

use DevTools\Messenger\Bridge\Amqp\Routing\RoutingStrategyInterface;

class HeadersConverter implements HeadersConverterInterface
{
    private const DEFAULT_SHARED_TYPE = 'array';

    private RoutingStrategyInterface $routingStrategy;

    private array $routingContext;

    private array $headersMap;

    public function __construct(
        RoutingStrategyInterface $routingStrategy,
        array $routingContext,
        array $headersMap
    ) {
        $this->routingStrategy = $routingStrategy;
        $this->routingContext = $routingContext;
        $this->headersMap = ['to' => $headersMap, 'from' => array_flip($headersMap)];
    }

    public function toSharedFormat(array $encodedEnvelope): array
    {
        $newHeaders = [];

        foreach ($encodedEnvelope['headers'] ?? [] as $name => $header) {
            $newName = $this->headersMap['to'][$name] ?? null;

            if ($newName) {
                $newHeaders[$newName] = $header;
            }
        }

        $newHeaders['type'] = $this->normalizeType($encodedEnvelope);

        return $newHeaders;
    }

    public function fromSharedFormat(array $encodedEnvelope): array
    {
        $newHeaders = [];

        foreach ($encodedEnvelope['headers'] ?? [] as $name => $header) {
            $newName = $this->headersMap['from'][$name] ?? null;

            if ($newName) {
                $newHeaders[$newName] = $header;
            }
        }

        $newHeaders['type'] = $this->denormalizeType($encodedEnvelope);

        return $newHeaders;
    }

    protected function denormalizeType(array $envelope): ?string
    {
        $type = $envelope['headers']['type'] ?? null;

        if (null === $type) {
            return null;
        }

        if (self::DEFAULT_SHARED_TYPE === $type) {
            return \ArrayObject::class;
        }

        return $this->routingStrategy->getClass($type, $this->routingContext + ['encoded_envelop' => $envelope]);
    }

    protected function normalizeType(array $envelope): ?string
    {
        $type = $envelope['headers']['type'] ?? null;

        if (null === $type) {
            return null;
        }

        if (\ArrayObject::class === $type) {
            return self::DEFAULT_SHARED_TYPE;
        }

        return $this->routingStrategy->getRoutingKey($type, $this->routingContext + ['encoded_envelop' => $envelope]);
    }
}
