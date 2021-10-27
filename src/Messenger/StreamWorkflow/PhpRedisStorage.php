<?php

declare(strict_types = 1);

namespace DevTools\Messenger\StreamWorkflow;

class PhpRedisStorage implements StorageInterface
{
    private const ONLY_ADD = 'NX';

    private const DEFAULT_DATA_TTL = 60 * 60 * 24 * 30;

    private \Redis $client;

    private ?int $dataTtl;

    public function __construct(\Redis $client, int $dataTtl = null)
    {
        $this->client = $client;
        $this->dataTtl = $dataTtl ?? self::DEFAULT_DATA_TTL;
    }

    public function getCurrentPosition(string $namespace, string $workflowId): ?int
    {
        $result = $this->client->zRange($this->getKey($namespace, $workflowId), -1, -1, true);

        return empty($result) ? null : (int) reset($result);
    }

    public function addItem(string $namespace, string $workflowId, int $position, string $status): void
    {
        $key = $this->getKey($namespace, $workflowId);

        $this->client->zAdd($key, [self::ONLY_ADD], $position, $this->buildValue($position, $status));
        $this->client->expire($key, $this->dataTtl);
    }

    public function setItemStatus(string $namespace, string $workflowId, int $position, string $status): void
    {
        $key = $this->getKey($namespace, $workflowId);

        $this->client->zRemRangeByScore($key, $position, $position);
        $this->client->zAdd($key, [self::ONLY_ADD], $position, $this->buildValue($position, $status));
    }

    public function getItemStatus(string $namespace, string $workflowId, int $position): ?string
    {
        $result = $this->client->zRangeByScore(
            $this->getKey($namespace, $workflowId),
            $position,
            $position,
            []
        );

        return isset($result[0]) ? $this->extractStatus($result[0]) : null;
    }

    public function removeItem(string $namespace, string $workflowId, int $position): void
    {
        $this->client->zRemRangeByScore($this->getKey($namespace, $workflowId), $position, $position);
    }

    private function getKey(string $namespace, string $workflowId): string
    {
        return 'stream_workflow:' . $namespace . ':' . $workflowId;
    }

    private function buildValue(int $position, string $status): string
    {
        return $position . '_' . $status;
    }

    private function extractStatus(string $value): string
    {
        return explode('_', $value)[1];
    }
}
