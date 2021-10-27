<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use DevTools\Messenger\Stamp\DefaultTransportStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;

class CommandBus
{
    private MessageBusInterface $messageBus;

    private ?string $defaultTransport;

    public function __construct(MessageBusInterface $messageBus, string $defaultTransport)
    {
        $this->messageBus = $messageBus;
        $this->defaultTransport = $defaultTransport;
    }

    /**
     * @param StampInterface[] $stamps
     *
     * @return mixed
     */
    public function dispatch(object $command, array $stamps = [])
    {
        $senderStamps = array_filter($stamps, fn (StampInterface $stamp) => $stamp instanceof DefaultTransportStamp);

        if (empty($senderStamps)) {
            $stamps[] = new DefaultTransportStamp($this->defaultTransport);
        }

        $envelope = $this->messageBus->dispatch($command, $stamps);

        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        return isset($handledStamps[0]) ? $handledStamps[0]->getResult() : null;
    }
}
