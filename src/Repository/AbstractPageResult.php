<?php

declare(strict_types = 1);

namespace DevTools\Repository;

abstract class AbstractPageResult
{
    protected array $items;

    protected int $itemsPerPage;

    public function __construct(array $items, int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
}
