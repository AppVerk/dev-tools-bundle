<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Validator\Constraints as Assert;

trait PaginatedListQueryTrait
{
    /**
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\Type("int")
     *
     * @var mixed
     */
    public $page = 1;

    /**
     * @Assert\Range(min="1", max="300")
     * @Assert\Type("int")
     *
     * @var mixed
     */
    public $limit = 10;
}
