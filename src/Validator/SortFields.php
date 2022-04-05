<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Composite;
use Symfony\Component\Validator\Constraints\Optional;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class SortFields extends Composite
{
    public const NO_SUCH_FIELD_ERROR = '7bb484df-0d25-4443-8a9b-36f7aabaf4de';

    public array $fields = [];

    public string $extraFieldsMessage = 'This field was not expected.';

    /**
     * @var string[]
     */
    protected static $errorNames = [
        self::NO_SUCH_FIELD_ERROR => 'NO_SUCH_FIELD_ERROR',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        $options = $this->normalizeOptions($options);
        $fields = $options['fields'] ?? null;

        if (is_array($fields)) {
            $constraintsMap = [];
            $constraint = new Optional(new Choice(['asc', 'desc']));

            foreach ($fields as $fieldName) {
                $constraintsMap[$fieldName] = $constraint;
            }

            $options['fields'] = $constraintsMap;
        }

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['fields'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompositeOption(): string
    {
        return 'fields';
    }

    /**
     * @param mixed $options
     */
    protected function normalizeOptions($options): array
    {
        if (is_array($options['value'] ?? null)) {
            return ['fields' => $options['value']];
        }

        if (is_array($options) && !array_intersect(array_keys($options), ['groups', 'fields', 'extraFieldsMessage'])) {
            return ['fields' => $options];
        }

        return $options;
    }
}
