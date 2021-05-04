<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UniqueEntityField extends Constraint
{
    public const NOT_UNIQUE_ERROR = '3744cfb0-cee5-4a50-b964-d6db1635b368';

    public string $message = 'This value is already used.';

    public string $em = 'default';

    public string $fieldName = 'id';

    public ?string $entityClass = null;

    public string $repositoryMethod = 'findBy';

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
        string $fieldName = null,
        string $em = null,
        string $repositoryMethod = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->entityClass = $entityClass ?? $this->entityClass;
        $this->message = $message ?? $this->message;
        $this->em = $em ?? $this->em;
        $this->repositoryMethod = $repositoryMethod ?? $this->repositoryMethod;
        $this->fieldName = $fieldName ?? $this->fieldName;
    }
}
