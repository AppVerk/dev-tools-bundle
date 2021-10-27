<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

class DefaultTransportStamp implements NonSendableStampInterface
{
    public const SYNC = 'sync';

    private string $transportName;

    public function __construct(string $transportName)
    {
        $this->transportName = $transportName;
    }

    public function getTransportName(): string
    {
        return $this->transportName;
    }

    public static function sync(): self
    {
        return new self(self::SYNC);
    }
}
