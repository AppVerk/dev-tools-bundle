<?php

declare(strict_types = 1);

namespace DevTools\Domain;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;

class CollectionFilter
{
    private Collection $collection;

    private string $field;

    /**
     * @var mixed
     */
    private $fieldValue;

    private ?string $accessor = null;

    private ?\Closure $comparator = null;

    /**
     * @param mixed $fieldValue
     */
    public function __construct(
        Collection $collection,
        string $field,
        $fieldValue,
        \Closure $comparator = null,
        string $accessor = null
    ) {
        $this->collection = $collection;
        $this->field = $field;
        $this->fieldValue = $fieldValue;
        $this->accessor = $accessor;
        $this->comparator = $comparator;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return $this->getResult()->first();
    }

    public function getResult(): Collection
    {
        if ($this->collection instanceof PersistentCollection && !$this->collection->isInitialized()) {
            $condition = Criteria::expr()->eq($this->field, $this->fieldValue);
            $criteria = Criteria::create()->where($condition);

            return $this->collection->matching($criteria);
        }

        $filterValue = $this->fieldValue;
        $accessor = $this->accessor ?? 'get' . ucfirst($this->field);
        $comparator = $this->comparator ?? static function ($filteredValue, $filterValue) {
            return $filteredValue === $filterValue;
        };

        return $this->collection->filter(static function (object $object) use ($accessor, $filterValue, $comparator) {
            return $comparator($object->{$accessor}(), $filterValue);
        });
    }
}
