<?php

declare(strict_types = 1);

namespace DevTools\FosRest\ParamConverter;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Existence;
use Symfony\Component\Validator\Constraints\Type;

class DataNormalizer
{
    public function normalize(?array $rawData, Constraint $constraint): ?array
    {
        $result = $rawData;

        if (null === $rawData) {
            return $result;
        }

        if ($constraint instanceof Collection) {
            foreach ($constraint->fields as $key => $field) {
                if (!$field instanceof Existence || !isset($result[$key])) {
                    continue;
                }

                foreach ($field->constraints as $nestedConstraint) {
                    if ($nestedConstraint instanceof Collection) {
                        $result[$key] = $this->normalize($result[$key], $nestedConstraint);
                    } elseif ($nestedConstraint instanceof Type) {
                        $result[$key] = $this->castValue($nestedConstraint->type, $result[$key]);
                    }
                }
            }
        }

        return $rawData;
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function castValue(string $type, $value)
    {
        if ('int' === $type) {
            $newValue = filter_var($value, \FILTER_VALIDATE_INT);

            return false === $newValue ? $value : $newValue;
        }

        if ('float' === $type) {
            $newValue = filter_var($value, \FILTER_VALIDATE_FLOAT);

            return false === $newValue ? $value : $newValue;
        }

        if ('bool' === $type) {
            $newValue = filter_var($value, FILTER_VALIDATE_BOOL);

            return null === $newValue ? $value : $newValue;
        }

        return $value;
    }
}
