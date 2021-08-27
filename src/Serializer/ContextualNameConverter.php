<?php

declare(strict_types = 1);

namespace DevTools\Serializer;

use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ContextualNameConverter implements AdvancedNameConverterInterface
{
    public const DISABLE_CONVERSION_TAG = 'disable_name_conversion';

    private ?NameConverterInterface $fallback;

    public function __construct(NameConverterInterface $fallback = null)
    {
        $this->fallback = $fallback;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(string $propertyName, string $class = null, string $format = null, array $context = [])
    {
        if (isset($context[self::DISABLE_CONVERSION_TAG])) {
            return $propertyName;
        }

        if ($this->fallback instanceof AdvancedNameConverterInterface) {
            return $this->fallback->normalize($propertyName, $class, $format, $context);
        }

        return $this->fallback->normalize($propertyName);
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize(string $propertyName, string $class = null, string $format = null, array $context = [])
    {
        if (isset($context[self::DISABLE_CONVERSION_TAG])) {
            return $propertyName;
        }

        if ($this->fallback instanceof AdvancedNameConverterInterface) {
            return $this->fallback->denormalize($propertyName, $class, $format, $context);
        }

        return $this->fallback->denormalize($propertyName);
    }
}
