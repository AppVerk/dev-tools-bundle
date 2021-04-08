<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class Resolver
{
    private const MAX_LEVEL = 4;

    private const REQUEST_PARAMETER = 'include';

    private Parser $parser;

    private RequestStack $requestStack;

    private RetrieverLocator $retrieverLocator;

    private NameConverterInterface $nameConverter;

    public function __construct(
        NameConverterInterface $nameConverter,
        RequestStack $requestStack,
        Parser $parser,
        RetrieverLocator $retrieverLocator
    ) {
        $this->parser = $parser;
        $this->requestStack = $requestStack;
        $this->retrieverLocator = $retrieverLocator;
        $this->nameConverter = $nameConverter;
    }

    /**
     * @param mixed $data
     */
    public function resolve($data, MapInterface $map): array
    {
        if (!is_iterable($data)) {
            $data = [$data];
        }

        $rawIncludes = (array) $this->requestStack->getMasterRequest()->get(self::REQUEST_PARAMETER, []);
        $includes = $this->parser->parse($this->normalizeIncludes($rawIncludes), self::MAX_LEVEL);

        $map->init($includes);

        return $this->resolveMap($map, $data);
    }

    private function resolveMap(MapInterface $map, iterable $data): array
    {
        $relationsIds = $map->extractRelationsIds($data);

        $result = [];

        foreach ($map->getChildren() as $relation => $child) {
            $retriever = $this->getDataRetriever($child);
            $ids = $relationsIds[$relation];

            $result[$relation] = 0 === count($ids) ? [] : $retriever->retrieve($ids, $map->getContext());
            $childResults = $this->resolveMap($child, $result[$relation]);

            foreach ($childResults as $childRelation => $childResult) {
                $relation = $this->nameConverter->normalize($relation . '.' . $childRelation);
                $result[$relation] = $childResult;
            }
        }

        return $result;
    }

    private function getDataRetriever(MapInterface $map): RetrieverInterface
    {
        return $this->retrieverLocator->locate($map);
    }

    private function normalizeIncludes(array $includes): array
    {
        $result = [];

        foreach ($includes as $include) {
            if (!is_string($include)) {
                continue;
            }

            $result[] = $this->nameConverter->denormalize($include);
        }

        return $result;
    }
}
