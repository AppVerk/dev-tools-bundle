<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\Mock;

use DevTools\Messenger\CommandBus as BaseCommandBus;

class CommandBus extends BaseCommandBus
{
    private bool $enabled = true;

    private array $queue = [];

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $command, bool $forceSyncProcessing = false)
    {
        if ($this->enabled) {
            return parent::dispatch($command, $forceSyncProcessing);
        }

        $this->queue[] = $command;
    }

    public function enable(): void
    {
        $this->enabled = true;

        foreach ($this->queue as $command) {
            parent::dispatch($command);
        }
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function clear(): void
    {
        $this->queue = [];
    }
}
