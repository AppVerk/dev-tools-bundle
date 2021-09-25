<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use DevTools\Messenger\Stamp\ForcedSenderStamp;
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
    public function dispatch(object $command, bool $forceSyncProcessing = false)
    {
        $stamps = [];

        if ($forceSyncProcessing) {
            $stamps[] = new ForcedSenderStamp('sync');
        }

        $envelope = $this->messageBus->dispatch($command, $stamps);

        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        return isset($handledStamps[0]) ? $handledStamps[0]->getResult() : null;
    }
}
