<?php

declare(strict_types = 1);

namespace DevTools\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractFilters
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract public function getQuery(): QueryBuilder;

    protected function createQueryBuilder(string $entityClass, string $alias): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias)
        ;
    }
}
