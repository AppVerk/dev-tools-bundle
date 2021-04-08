<?php

declare(strict_types = 1);

namespace DevTools\Repository;

use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination as BundleSlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator as KnpPaginator;

class Paginator
{
    private KnpPaginator $paginator;

    public function __construct(KnpPaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param DBALQueryBuilder|NativeQuery|QueryBuilder $queryBuilder
     */
    public function paginate(
        $queryBuilder,
        int $page,
        int $itemsPerPage,
        string $rootEntityFilter = null,
        bool $forceDistinctQuery = false
    ): PaginatedResult {
        if ($queryBuilder instanceof NativeQuery) {
            return $this->buildPaginatedResultFromNativeQuery($queryBuilder, $page, $itemsPerPage);
        }

        $filerParameter = null === $rootEntityFilter ? null : $queryBuilder->getParameter($rootEntityFilter);

        if (null !== $filerParameter && count((array) $filerParameter->getValue()) === $itemsPerPage) {
            return $this->buildSinglePageResult($queryBuilder, $itemsPerPage);
        }

        /** @var BundleSlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $queryBuilder,
            $page,
            $itemsPerPage,
            ['sortFieldParameterName' => null, 'distinct' => $forceDistinctQuery, 'wrap-queries' => true]
        );

        $this->assertPagination($pagination);

        return $this->buildMultyPageResult($pagination, $itemsPerPage);
    }

    private function buildPaginatedResultFromNativeQuery(NativeQuery $query, int $page, int $itemsPerPage): PaginatedResult
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total');

        $countQuery = clone $query;
        $countQuery->setSql(preg_replace('/SELECT(.*)FROM/iU', 'SELECT count(*) as total FROM', $query->getSQl(), 1));
        $countQuery->setParameters($query->getParameters());
        $countQuery->setResultSetMapping($rsm);

        $offset = ($page - 1) * $itemsPerPage;
        $total = (int) $countQuery->getSingleScalarResult();
        $result = [];

        if ($total) {
            $paginatedQuery = clone $query;
            $paginatedQuery->setSql($query->getSQl() . ' LIMIT ' . $offset . ',' . $itemsPerPage);
            $paginatedQuery->setParameters($query->getParameters());
            $result = $paginatedQuery->getResult();
        }

        return new PaginatedResult(
            $result,
            $total,
            $page,
            (int) ceil($total / $itemsPerPage),
            $itemsPerPage
        );
    }

    private function assertPagination(PaginationInterface $pagination): void
    {
        if ($pagination instanceof SlidingPagination) {
            return;
        }

        if ($pagination instanceof BundleSlidingPagination) {
            return;
        }

        throw new \RuntimeException('Unsupported pagination instance provided.');
    }

    private function buildMultyPageResult(BundleSlidingPagination $pagination, int $itemsPerPage): PaginatedResult
    {
        $data = $pagination->getPaginationData();
        $items = [];

        foreach ($pagination->getItems() as $item) {
            $items[] = $item;
        }

        return new PaginatedResult(
            $items,
            (int) $pagination->getTotalItemCount(),
            $data['current'],
            $data['pageCount'],
            $itemsPerPage
        );
    }

    /**
     * @param DBALQueryBuilder|QueryBuilder $queryBuilder
     */
    private function buildSinglePageResult($queryBuilder, int $itemsPerPage): PaginatedResult
    {
        $items = [];

        if ($queryBuilder instanceof DBALQueryBuilder) {
            $items = $queryBuilder->execute()->fetchAllAssociative();
        } elseif ($queryBuilder instanceof QueryBuilder) {
            $items = $queryBuilder->getQuery()->getResult();
        }

        return new PaginatedResult($items, count($items), 1, 1, $itemsPerPage);
    }
}
