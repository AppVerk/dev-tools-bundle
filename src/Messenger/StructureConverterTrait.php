<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

trait StructureConverterTrait
{
    protected StructureConverter $structureConverter;

    public function setStructureConverter(StructureConverter $structureConverter): void
    {
        $this->structureConverter = $structureConverter;
    }

    protected function convertTo($value, string $class)
    {
        return $this->structureConverter->toObject($value, $class);
    }
}
