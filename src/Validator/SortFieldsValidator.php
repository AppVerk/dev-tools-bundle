<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SortFieldsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SortFields) {
            throw new UnexpectedTypeException($constraint, SortFields::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_array($value) && !($value instanceof \Traversable && $value instanceof \ArrayAccess)) {
            throw new UnexpectedValueException($value, 'array|(Traversable&ArrayAccess)');
        }

        $context = $this->context;

        foreach ($constraint->fields as $field => $fieldConstraint) {
            $existsInArray = is_array($value) && array_key_exists($field, $value);
            $existsInArrayAccess = $value instanceof \ArrayAccess && $value->offsetExists($field);

            if ($existsInArray || $existsInArrayAccess) {
                if (count($fieldConstraint->constraints) > 0) {
                    $context->getValidator()
                        ->inContext($context)
                        ->atPath('[' . $field . ']')
                        ->validate($value[$field], $fieldConstraint->constraints)
                    ;
                }
            }
        }

        foreach ($value as $field => $fieldValue) {
            if (!isset($constraint->fields[$field])) {
                $context->buildViolation($constraint->extraFieldsMessage)
                    ->atPath('[' . $field . ']')
                    ->setParameter('{{ field }}', $this->formatValue($field))
                    ->setInvalidValue($fieldValue)
                    ->setCode(SortFields::NO_SUCH_FIELD_ERROR)
                    ->addViolation()
                ;
            }
        }
    }
}
