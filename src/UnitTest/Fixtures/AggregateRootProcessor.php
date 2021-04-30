<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\Fixtures;

use DevTools\Domain\AbstractAggregateRoot;
use Fidry\AliceDataFixtures\ProcessorInterface;

class AggregateRootProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function preProcess(string $fixtureId, $object): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess(string $fixtureId, $object): void
    {
        if ($object instanceof AbstractAggregateRoot) {
            $object->popRecordedEvents();
        }
    }
}
