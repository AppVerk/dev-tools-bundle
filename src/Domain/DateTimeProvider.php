<?php

declare(strict_types = 1);

namespace DevTools\Domain;

use DateTimeImmutable;

class DateTimeProvider
{
    public function current(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
