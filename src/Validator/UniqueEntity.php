<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UniqueEntity extends Constraint
{
    public const NOT_UNIQUE_ERROR = '3744cfb0-cee5-4a50-b964-d6db1635b368';

    public string $message = 'This value is already used.';

    public ?string $entityClass = null;

    public string $repositoryMethod = 'exists';

    public ?string $excludeField = null;

    /**
     * @var string[]
     */
    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(
        array $options = [],
        string $entityClass = null,
        string $message = null,
        string $repositoryMethod = null,
        string $excludeField = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->entityClass = $entityClass ?? $this->entityClass;
        $this->message = $message ?? $this->message;
        $this->repositoryMethod = $repositoryMethod ?? $this->repositoryMethod;
        $this->excludeField = $excludeField ?? $this->excludeField;
    }
}
