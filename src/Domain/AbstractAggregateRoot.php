<?php

declare(strict_types = 1);

namespace DevTools\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractAggregateRoot
{
    use EventingTrait;

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

    public function attachEvent(AbstractAggregateRootEvent $event): void
    {
        ++$this->version;

        $this->recordedEvents[] = $event->withVersion($this->version);
    }

    protected function recordThat(AbstractAggregateRootEvent $event): void
    {
        $this->attachEvent($event);
        $this->apply($event);
    }
}
