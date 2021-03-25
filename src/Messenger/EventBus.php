<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

class EventBus
{
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function dispatch(object $event): void
    {
        $this->messageBus->dispatch($event);
    }
}
