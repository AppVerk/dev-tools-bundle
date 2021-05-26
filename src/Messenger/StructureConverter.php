<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class StructureConverter
{
    private ObjectNormalizer $objectNormalizer;

    public function __construct(ObjectNormalizer $objectNormalizer)
    {
        $this->objectNormalizer = $objectNormalizer;
    }

    /**
     * @return mixed|object
     */
    public function toObject(AbstractStructure $structure, string $class)
    {
        return $this->objectNormalizer->denormalize($structure->toArray(), $class);
    }
}
