<?php

declare(strict_types = 1);

namespace DevTools\Repository;

class PaginatedResult extends AbstractPageResult
{
    private int $totalItemsCount;

    private int $currentPage;

    private int $pageCount;

    public function __construct(array $items, int $totalItemsCount, int $currentPage, int $pageCount, int $itemsPerPage)
    {
        parent::__construct($items, $itemsPerPage);

        $this->totalItemsCount = $totalItemsCount;
        $this->currentPage = $currentPage;
        $this->pageCount = $pageCount;
    }

    public function getTotalItemsCount(): int
    {
        return $this->totalItemsCount;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
