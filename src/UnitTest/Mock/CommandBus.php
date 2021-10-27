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
    public function dispatch(object $command, array $stamps = [])
    {
        if ($this->enabled) {
            return parent::dispatch($command, $stamps);
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
