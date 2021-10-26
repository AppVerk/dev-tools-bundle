<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Envelope;

use DevTools\Messenger\Stamp\DefaultTransportStamp;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class SenderLocator implements SendersLocatorInterface
{
    private SendersLocatorInterface $decorated;

    private ContainerInterface $sendersLocator;

    public function __construct(SendersLocatorInterface $decorated, ContainerInterface $sendersLocator)
    {
        $this->decorated = $decorated;
        $this->sendersLocator = $sendersLocator;
    }

    public function getSenders(Envelope $envelope): iterable
    {
        foreach ($this->decorated->getSenders($envelope) as $senderAlias => $sender) {
            yield $senderAlias => $sender;
        }

        /** @var null|DefaultTransportStamp $stamp */
        $stamp = $envelope->last(DefaultTransportStamp::class);

        if (null === $stamp || isset($sender)) {
            return;
        }

        $senderAlias = $stamp->getTransportName();

        if ($senderAlias === DefaultTransportStamp::SYNC) {
            return;
        }

        if (!$this->sendersLocator->has($senderAlias)) {
            throw new RuntimeException(sprintf(
                'Invalid senders configuration: sender "%s" is not in the senders locator.',
                $senderAlias
            ));
        }

        yield $senderAlias => $this->sendersLocator->get($senderAlias);
    }
}
