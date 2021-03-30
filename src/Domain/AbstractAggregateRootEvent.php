<?php

declare(strict_types = 1);

namespace DevTools\Domain;

abstract class AbstractAggregateRootEvent
{
    protected string $aggregateId;

    protected \DateTimeImmutable $createdAt;

    protected int $version;

    public function __construct(string $aggregateId, \DateTimeImmutable $createdAt = null, int $version = null)
    {
        $this->aggregateId = $aggregateId;
        $this->createdAt = $createdAt ?? DateTimeProvider::current();
        $this->version = $version ?? 0;
    }

    public function withVersion(int $version): self
    {
        $self = clone $this;

        $self->version = $version;

        return $self;
    }

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
