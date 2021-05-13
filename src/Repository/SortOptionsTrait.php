<?php

declare(strict_types = 1);

namespace DevTools\Repository;

use Doctrine\ORM\QueryBuilder;

trait SortOptionsTrait
{
    /**
     * @var string[]
     */
    private array $sortOptions = [];

    /**
     * @return string[]
     */
    public function getSortOptions(): array
    {
        return $this->sortOptions;
    }

    /**
     * @param string[] $sortOptions
     *
     * @return static
     */
    public function setSortOptions(array $sortOptions): self
    {
        $this->sortOptions = $sortOptions;

        return $this;
    }

    protected function sortByIdenticalFields(array $sortOptions, QueryBuilder $queryBuilder): void
    {
        $alias = $queryBuilder->getRootAliases()[0] ?? null;

        if (null === $alias) {
            throw new \RuntimeException('No alias was set before invoking getRootAlias().');
        }

        foreach ($sortOptions as $fieldName => $sortDirection) {
            $queryBuilder->addOrderBy($alias . '.' . $fieldName, $sortDirection);
        }
    }
}
