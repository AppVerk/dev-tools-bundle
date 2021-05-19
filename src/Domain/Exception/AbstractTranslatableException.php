<?php

declare(strict_types = 1);

namespace DevTools\Domain\Exception;

use Throwable;

abstract class AbstractTranslatableException extends \RuntimeException implements TranslatableExceptionInterface
{
    protected string $messageKey;

    protected array $messageData = [];

    public function __construct(string $messageKey, array $messageData = [], int $code = 0, Throwable $previous = null)
    {
        $message = str_replace(array_keys($messageData), array_map('strval', $messageData), $messageKey);

        parent::__construct($message, $code, $previous);

        $this->messageKey = $messageKey;
        $this->messageData = $messageData;
    }

    public function getMessageKey(): string
    {
        return $this->messageKey;
    }

    public function getMessageData(): array
    {
        return $this->messageData;
    }
}
