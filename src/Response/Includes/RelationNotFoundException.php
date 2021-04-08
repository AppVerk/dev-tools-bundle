<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

use RuntimeException;

class RelationNotFoundException extends RuntimeException
{
    public static function withName(string $name): self
    {
        return new self(sprintf('Relation "%s" not found', $name));
    }
}
