<?php

declare(strict_types = 1);

namespace DevTools\Domain;

class DateTimeProvider
{
    public static function current(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
