<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

use DevTools\Messenger\QueryBus;
use DevTools\Repository\AbstractPageResult;

abstract class AbstractRetriever implements RetrieverInterface
{
    protected QueryBus $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    protected function getList(object $query): array
    {
        $result = $this->queryBus->dispatch($query);

        if ($result instanceof AbstractPageResult) {
            return array_values($result->getItems());
        }

        return (array) $result;
    }
}
