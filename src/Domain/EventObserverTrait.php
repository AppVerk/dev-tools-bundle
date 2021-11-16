<?php

declare(strict_types = 1);

namespace DevTools\Domain;

trait EventObserverTrait
{
    protected function observe(AbstractAggregateRootEvent $event): void
    {
        $handler = $this->determineEventHandlerMethodFor($event);

        if (\method_exists($this, $handler)) {
            $this->{$handler}($event);
        }
    }

    abstract protected function determineEventHandlerMethodFor(AbstractAggregateRootEvent $event): string;
}
