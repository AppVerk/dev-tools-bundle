<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use DevTools\Enum\AbstractEnum;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Enum extends Choice
{
    /**
     * @var null|class-string
     */
    public ?string $class = null;

    /**
     * @param null|class-string $class
     * @param null|mixed        $groups
     * @param null|mixed        $payload
     */
    public function __construct(
        array $options = [],
        string $class = null,
        bool $multiple = null,
        bool $strict = null,
        int $min = null,
        int $max = null,
        string $message = null,
        string $multipleMessage = null,
        string $minMessage = null,
        string $maxMessage = null,
        $groups = null,
        $payload = null
    ) {
        $this->class = $class;

        $callback = function () {
            $class = $this->class;

            if (null === $class || !is_subclass_of($class, AbstractEnum::class)) {
                throw new ConstraintDefinitionException(sprintf(
                    'The Enum constraint expects class parameter to be subclass of %s.',
                    AbstractEnum::class
                ));
            }

            return array_values($class::toArray());
        };

        parent::__construct(
            null,
            $callback,
            $multiple,
            $strict,
            $min,
            $max,
            $message,
            $multipleMessage,
            $minMessage,
            $maxMessage,
            $groups,
            $payload,
            $options
        );
    }

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return ChoiceValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'class';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['class'];
    }
}
