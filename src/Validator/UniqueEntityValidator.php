<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends AbstractEntityValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($registry);

        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param mixed $value
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        /** @var class-string|null $entityClass */
        $entityClass = $constraint->entityClass;

        if (null === $entityClass) {
            throw new ConstraintDefinitionException('Entity class should be fined.');
        }

        if (null === $value) {
            return;
        }

        $em = $this->registry->getManagerForClass($entityClass);
        $class = $em->getClassMetadata($entityClass);

        $excludeValue = $this->getExcludedValue($this->context->getRoot(), $constraint->excludeField);

        $result = $this->assertEntityExists(
            $em,
            $entityClass,
            $constraint->repositoryMethod,
            $value,
            $excludeValue
        );

        if (!$result) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($em, $class, $value))
            ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
            ->setCause($result)
            ->addViolation()
        ;
    }

    /**
     * @return null|mixed
     */
    private function getExcludedValue(?object $object, ?string $excludeField)
    {
        if (null === $object || null === $excludeField) {
            return null;
        }

        try {
            return $this->propertyAccessor->getValue($object, $excludeField);
        } catch (NoSuchPropertyException $exception) {
            throw new ConstraintDefinitionException(
                sprintf('Invalid excluded field "%s" provided.', $excludeField),
                0,
                $exception
            );
        }
    }
}
