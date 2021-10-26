<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class CommandBusScheduler
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function schedule(object $command, \DateTimeImmutable $date): void
    {
        $this->messageBus->dispatch($command, [DelayStamp::delayUntil($date)]);
    }
}
