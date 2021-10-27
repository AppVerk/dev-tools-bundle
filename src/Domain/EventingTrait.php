<?php

declare(strict_types = 1);

namespace DevTools\Domain;

trait EventingTrait
{
    protected function apply(AbstractAggregateRootEvent $event): void
    {
        $handler = $this->determineEventHandlerMethodFor($event);

        if (!\method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                static::class
            ));
        }

        $this->{$handler}($event);
    }

    protected function determineEventHandlerMethodFor(AbstractAggregateRootEvent $event): string
    {
        return 'when' . implode(array_slice(explode('\\', get_class($event)), -1));
    }
}
