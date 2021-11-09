<?php

declare(strict_types = 1);

namespace DevTools\Repository;

class PaginatedResult extends AbstractPageResult
{
    private int $itemsTotalCount;

    private int $currentPage;

    private int $pageCount;

    public function __construct(array $items, int $itemsTotalCount, int $currentPage, int $pageCount, int $itemsPerPage)
    {
        parent::__construct($items, $itemsPerPage);

        $this->itemsTotalCount = $itemsTotalCount;
        $this->currentPage = $currentPage;
        $this->pageCount = $pageCount;
    }

    public function getItemsTotalCount(): int
    {
        return $this->itemsTotalCount;
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
