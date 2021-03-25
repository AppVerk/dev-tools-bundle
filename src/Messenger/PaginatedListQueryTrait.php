<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Validator\Constraints as Assert;

trait PaginatedListQueryTrait
{
    /**
     * @Assert\GreaterThanOrEqual(1)
     */
    public int $page = 1;

    /**
     * @Assert\Range(min="1", max="300")
     */
    public int $limit = 10;
}
