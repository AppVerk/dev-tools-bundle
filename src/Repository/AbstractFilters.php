<?php

declare(strict_types = 1);

namespace DevTools\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractFilters
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
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
