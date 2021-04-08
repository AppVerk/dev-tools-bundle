<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

abstract class AbstractNoRelationsMap extends AbstractMap
{
    public function getRelationsMap(): array
    {
        return [];
    }

    public function extractRelationsIds(iterable $items): array
    {
        return [];
    }
}
