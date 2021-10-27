<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

abstract class AbstractProcessManager
{
    protected CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    protected function dispatchCommand(object $command, StampInterface ...$stamps): void
    {
        $this->commandBus->dispatch($command, $stamps);
    }
}
