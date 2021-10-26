<?php
declare(strict_types=1);

namespace DevTools\Messenger\StreamWorkflow;

interface StorageInterface
{
    public function getCurrentPosition(string $namespace, string $workflowId): ?int;

    public function addItem(string $namespace, string $workflowId, int $position, string $status): void;

    public function setItemStatus(string $namespace, string $workflowId, int $position, string $status): void;

    public function getItemStatus(string $namespace, string $workflowId, int $position): ?string;

    public function removeItem(string $namespace, string $workflowId, int $position): void;
}