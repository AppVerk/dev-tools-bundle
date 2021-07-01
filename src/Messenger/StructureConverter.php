<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class StructureConverter
{
    private DenormalizerInterface $denormalizer;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|object
     */
    public function toObject($value, string $class)
    {
        return $this->denormalizer->denormalize(
            $value instanceof AbstractStructure ? $value->toArray() : $value,
            $class
        );
    }
}
