<?php

declare(strict_types = 1);

namespace DevTools\Domain;

class DateTimeProvider
{
    private static ?\DateTimeImmutable $currentDate = null;

    public static function current(): \DateTimeImmutable
    {
        return self::$currentDate ?? new \DateTimeImmutable();
    }

    public static function setCurrentDate(\DateTimeImmutable $date): void
    {
        self::$currentDate = $date;
    }

    public static function reset(): void
    {
        self::$currentDate = null;
    }
}
