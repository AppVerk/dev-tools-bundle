<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

abstract class AbstractProcessManager
{
    protected CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }
}
