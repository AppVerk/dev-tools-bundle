<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use DevTools\Serializer\ContextualNameConverter;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ObjectMapper
{
    private const COLLECTION_MARKER = '[]';

    private DenormalizerInterface $denormalizer;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function toObject($value, string $class)
    {
        if (null === $value) {
            return $value;
        }

        return $this->denormalizer->denormalize(
            $this->normalizeValue($value, $class),
            $class,
            null,
            [ContextualNameConverter::DISABLE_CONVERSION_TAG => true]
        );
    }

    protected function convertObjectToArray(object $object): array
    {
        $values = get_object_vars($object);

        foreach ($values as $key => $value) {
            if (is_object($value)) {
                $values[$key] = $this->convertObjectToArray($value);

                continue;
            }

            if (is_array($value)) {
                $values[$key] = [];

                foreach ($value as $index => $item) {
                    $values[$key][$index] = is_object($item) ? $this->convertObjectToArray($item) : $item;
                }
            }
        }

        return $values;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function normalizeValue($value, string $class = null)
    {
        if (is_object($value)) {
            return $this->convertObjectToArray($value);
        }

        if (is_array($value) && null !== $class && self::COLLECTION_MARKER === mb_substr($class, -2)) {
            return array_map(function ($item) {
                return $this->normalizeValue($item);
            }, $value);
        }

        return $value;
    }
}
