<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Envelope;

use DevTools\Messenger\Stamp\ForcedSenderStamp;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class ForcedSenderLocator implements SendersLocatorInterface
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
        /** @var null|ForcedSenderStamp $stamp */
        $stamp = $envelope->last(ForcedSenderStamp::class);

        if (null === $stamp) {
            foreach ($this->decorated->getSenders($envelope) as $senderAlias => $sender) {
                yield $senderAlias => $sender;
            }

            return;
        }

        $senderAlias = $stamp->getSenderAlias();

        if (!$this->sendersLocator->has($senderAlias)) {
            throw new RuntimeException(sprintf(
                'Invalid senders configuration: sender "%s" is not in the senders locator.',
                $senderAlias
            ));
        }

        yield $senderAlias => $this->sendersLocator->get($senderAlias);
    }
}
