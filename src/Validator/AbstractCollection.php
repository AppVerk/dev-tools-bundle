<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\CollectionValidator;

abstract class AbstractCollection extends Collection
{
    public function __construct($options = null)
    {
        $options = (array) $options;

        $options['fields'] = $this->getFieldsConstraints();

        parent::__construct($options);
    }

    public function validatedBy()
    {
        return CollectionValidator::class;
    }

    abstract protected function getFieldsConstraints(): array;
}
