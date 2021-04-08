<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

interface RetrieverInterface
{
    public function retrieve(array $ids, array $context): array;
}
