<?php

declare(strict_types = 1);

namespace DevTools\Domain\Exception;

interface TranslatableExceptionInterface
{
    public function getMessageKey(): string;

    public function getMessageData(): array;
}
