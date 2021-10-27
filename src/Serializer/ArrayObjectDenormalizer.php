<?php

declare(strict_types = 1);

namespace DevTools\Serializer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ArrayObjectDenormalizer implements CacheableSupportsMethodInterface, DenormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new \ArrayObject((array) $data);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return \ArrayObject::class === $type;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === static::class;
    }
}
