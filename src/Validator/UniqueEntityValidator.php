<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends AbstractEntityValidator
{
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

        if (null === $constraint->entityClass) {
            throw new ConstraintDefinitionException('Entity class should be fined.');
        }

        if (null === $value) {
            return;
        }

        $em = $this->registry->getManagerForClass($constraint->entityClass);
        $class = $em->getClassMetadata($constraint->entityClass);

        $result = $this->assertEntityExists($em, $constraint->entityClass, $constraint->repositoryMethod, $value);

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
}
