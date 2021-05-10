<?php

declare(strict_types = 1);

namespace DevTools\Enum;

use MyCLabs\Enum\Enum;

abstract class AbstractEnum extends Enum
{
    public function isOneOf(self ...$values): bool
    {
        foreach ($values as $value) {
            if ($this->equals($value)) {
                return true;
            }
        }

        return false;
    }
}
