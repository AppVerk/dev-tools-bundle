<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EntityExistValidator extends AbstractEntityValidator
{
    /**
     * @param mixed $value
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityExist) {
            throw new UnexpectedTypeException($constraint, EntityExist::class);
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

        $result = $this->assertEntityExists($em, $entityClass, $constraint->repositoryMethod, $value);

        if ($result) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($em, $class, $value))
            ->setCode(EntityExist::NOT_FOUND_ERROR)
            ->setCause($result)
            ->addViolation()
        ;
    }
}
