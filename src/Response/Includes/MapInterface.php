<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

interface MapInterface
{
    public function __construct(array $context = [], string $retrieverClass = null);

    public function init(array $includes): void;

    public function getRelationsMap(): array;

    public function extractRelationsIds(iterable $items): array;

    /**
     * @return MapInterface[]
     */
    public function getChildren(): array;

    public function getRetrieverClass(): ?string;

    public function getContext(): array;
}
