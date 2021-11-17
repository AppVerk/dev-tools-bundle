<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class EntityExist extends Constraint
{
    public const NOT_FOUND_ERROR = 'cd100ad1-91fd-4f46-a45f-49bf7a032d70';

    public string $message = 'Related entity not found.';

    /**
     * @var null|class-string
     */
    public ?string $entityClass = null;

    public string $repositoryMethod = 'exists';

    /**
     * @var string[]
     */
    protected static $errorNames = [
        self::NOT_FOUND_ERROR => 'NOT_FOUND_ERROR',
    ];

    /**
     * @param mixed[]           $options
     * @param null|class-string $entityClass
     * @param null|string[]     $groups
     * @param mixed             $payload
     */
    public function __construct(
        array $options = [],
        string $entityClass = null,
        string $message = null,
        string $repositoryMethod = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->entityClass = $entityClass ?? $this->entityClass;
        $this->message = $message ?? $this->message;
        $this->repositoryMethod = $repositoryMethod ?? $this->repositoryMethod;
    }
}
