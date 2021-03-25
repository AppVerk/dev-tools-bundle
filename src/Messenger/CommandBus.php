<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @return mixed
     */
    public function dispatch(object $command)
    {
        $envelope = $this->messageBus->dispatch($command);

        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        return isset($handledStamps[0]) ? $handledStamps[0]->getResult() : null;
    }
}
