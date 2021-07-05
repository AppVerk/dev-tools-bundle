<?php

declare(strict_types = 1);

namespace DevTools\FosRest\ParamConverter;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Composite;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Mapping\CascadingStrategy;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\MetadataInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataNormalizer
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function normalize(?array $rawData, string $class): ?array
    {
        if (null === $rawData || !$this->validator->hasMetadataFor($class)) {
            return $rawData;
        }

        $metadata = $this->validator->getMetadataFor($class);

        return $this->normalizeData($metadata, $rawData);
    }

    /**
     * @param mixed $value
     *
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

        if ('string' === $type) {
            if (is_scalar($value) || is_object($value) && method_exists($value, '__toString')) {
                return (string) $value;
            }

            return $value;
        }

        return $value;
    }

    /**
     * @param ClassMetadata|MetadataInterface $metadata
     */
    private function normalizeData(MetadataInterface $metadata, array $rawData): array
    {
        foreach ($metadata->properties as $property) {
            if (!isset($rawData[$property->property])) {
                continue;
            }

            $propertyType = $metadata
                ->getReflectionClass()
                ->getProperty($property->getName())
                ->getType()
            ;

            if (null !== $propertyType && !$propertyType->isBuiltin()) {
                if (CascadingStrategy::CASCADE !== $property->cascadingStrategy) {
                    continue;
                }

                if (!$this->validator->hasMetadataFor($propertyType->getName())) {
                    continue;
                }

                $rawData[$property->property] = $this->normalizeData(
                    $this->validator->getMetadataFor($propertyType->getName()),
                    $rawData[$property->property]
                );

                continue;
            }

            foreach ($property->constraints as $constraint) {
                $type = $this->determineTypeByConstraint($constraint);

                if (null === $type) {
                    continue;
                }

                $rawData[$property->property] = $this->castValue($type, $rawData[$property->property]);
            }
        }

        return $rawData;
    }

    private function determineTypeByConstraint(Constraint $constraint): ?string
    {
        if ($constraint instanceof Composite) {
            foreach ($constraint->getNestedContraints() as $nestedContraint) {
                $result = $this->determineTypeByConstraint($nestedContraint);

                if (null !== $result) {
                    return $result;
                }
            }
        }

        switch (get_class($constraint)) {
            case Uuid::class:
            case Length::class:
            case DateTime::class:
                return 'string';
            case Choice::class:
                return $constraint->multiple ? null : 'string';
            case Type::class:
                return $constraint->type;
        }

        return null;
    }
}
