<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

class ForcedSenderStamp implements NonSendableStampInterface
{
    private string $senderAlias;

    public function __construct(string $senderAlias)
    {
        $this->senderAlias = $senderAlias;
    }

    public function getSenderAlias(): ?string
    {
        return $this->senderAlias;
    }
}
