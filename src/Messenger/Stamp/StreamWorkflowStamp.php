<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class StreamWorkflowStamp implements StampInterface
{
    private string $namespace;

    private string $workflowId;

    private int $currentPosition;

    private ?int $requiredPosition;

    public function __construct(string $namespace, string $workflowId, int $currentPosition, int $requiredPosition = null)
    {
        $this->namespace = $namespace;
        $this->workflowId = $workflowId;
        $this->currentPosition = $currentPosition;
        $this->requiredPosition = $requiredPosition;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getWorkflowId(): string
    {
        return $this->workflowId;
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }

    public function getRequiredPosition(): ?int
    {
        return $this->requiredPosition;
    }

    public function withRequiredPosition(?int $requiredVersion): self
    {
        $self = clone $this;
        $self->requiredPosition = $requiredVersion;

        return $self;
    }
}
