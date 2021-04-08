<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

abstract class AbstractMap implements MapInterface
{
    /**
     * @var MapInterface[]
     */
    protected array $children = [];

    protected ?string $retrieverClass = null;

    private array $context;

    public function __construct(array $context = [], string $retrieverClass = null)
    {
        $this->context = $context;
        $this->retrieverClass = $retrieverClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetrieverClass(): ?string
    {
        return $this->retrieverClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function init(array $includes): void
    {
        $relationsMap = $this->getRelationsMap();
        $this->children = [];

        foreach ($includes as $key => $value) {
            $relationConfig = $relationsMap[$key] ?? null;

            if (null === $relationConfig) {
                throw RelationNotFoundException::withName($key);
            }

            $childRetrieverClass = is_array($relationConfig) ? $relationConfig[1] : null;
            $childMapClass = is_array($relationConfig) ? $relationConfig[0] : $relationConfig;

            /** @var MapInterface $child */
            $child = new $childMapClass($this->context, $childRetrieverClass);
            $child->init($includes[$key] ?? []);

            $this->children[$key] = $child;
        }
    }
}
