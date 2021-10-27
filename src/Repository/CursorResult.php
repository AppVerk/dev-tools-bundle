<?php

declare(strict_types = 1);

namespace DevTools\Repository;

class CursorResult extends AbstractPageResult
{
    private ?string $nextPageToken;

    public function __construct(array $items, int $itemsPerPage, string $nextPageToken = null)
    {
        parent::__construct($items, $itemsPerPage);

        $this->nextPageToken = $nextPageToken;
    }

    public function getNextPageToken(): ?string
    {
        return $this->nextPageToken;
    }
}
