<?php

declare(strict_types = 1);

namespace DevTools\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractAggregateRoot
{
    /**
     * @ORM\Column(type="integer")
     */
    protected int $version = 0;

    /**
     * @var AbstractAggregateRootEvent[]
     */
    protected array $recordedEvents = [];

    protected function __construct(AbstractAggregateRootEvent $event = null)
    {
        if (null !== $event) {
            $this->recordThat($event);
        }
    }

    /**
     * @return AbstractAggregateRootEvent[]
     */
    public function popRecordedEvents(): array
    {
        $pendingEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $pendingEvents;
    }

    protected function recordThat(AbstractAggregateRootEvent $event): void
    {
        ++$this->version;

        $this->recordedEvents[] = $event->withVersion($this->version);

        $this->apply($event);
    }

    protected function apply(AbstractAggregateRootEvent $event): void
    {
        $handler = $this->determineEventHandlerMethodFor($event);

        if (!\method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                get_class($this)
            ));
        }

        $this->{$handler}($event);
    }

    protected function determineEventHandlerMethodFor(AbstractAggregateRootEvent $event): string
    {
        return 'when' . implode(array_slice(explode('\\', get_class($event)), -1));
    }
}
