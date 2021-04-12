<?php

declare(strict_types = 1);

namespace DevTools\Domain;

use DevTools\Messenger\CommandBus;

abstract class AbstractProcessManager
{
    protected CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }
}
