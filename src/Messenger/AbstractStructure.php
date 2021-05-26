<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

abstract class AbstractStructure
{
    public function toArray(): array
    {
        $values = get_object_vars($this);

        return $this->convertValuesToArray($values);
    }

    protected function convertValuesToArray(array $values): array
    {
        foreach ($values as $key => $value) {
            if ($value instanceof self) {
                $values[$key] = $value->toArray();

                continue;
            }

            if (is_array($value)) {
                $values[$key] = array_map([$this, 'convertValuesToArray'], $value);
            }
        }

        return $values;
    }
}
