<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Middleware;

use DevTools\Messenger\Bridge\Amqp\Routing\RoutingStrategyInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceivedStamp;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class DynamicRoutingMiddleware implements MiddlewareInterface
{
    private RoutingStrategyInterface $strategy;

    private array $routingContext;

    public function __construct(RoutingStrategyInterface $strategy, array $routingContext)
    {
        $this->strategy = $strategy;
        $this->routingContext = $routingContext;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null === $envelope->last(AmqpStamp::class) && null === $envelope->last(AmqpReceivedStamp::class)) {
            $class = get_class($envelope->getMessage());
            $routingKey = $this->strategy->getRoutingKey(
                $class,
                $this->routingContext + ['envelop' => $envelope]
            );

            if (null === $routingKey) {
                throw new \RuntimeException(sprintf('Unable to determine routing key for class "%s"', $class));
            }

            $envelope = $envelope->with(new AmqpStamp($routingKey));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
