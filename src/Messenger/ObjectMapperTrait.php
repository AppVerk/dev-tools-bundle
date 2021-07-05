<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

trait ObjectMapperTrait
{
    protected ObjectMapper $objectMapper;

    public function setObjectMapper(ObjectMapper $objectMapper): void
    {
        $this->objectMapper = $objectMapper;
    }

    protected function convertTo($value, string $class)
    {
        return $this->objectMapper->toObject($value, $class);
    }
}
