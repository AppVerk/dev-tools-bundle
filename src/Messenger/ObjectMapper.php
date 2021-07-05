<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ObjectMapper
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
        if (null === $value) {
            return $value;
        }

        return $this->denormalizer->denormalize(
            is_object($value) ? $this->convertObjectToArray($value) : $value,
            $class
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
}
