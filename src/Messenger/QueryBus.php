<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @return mixed
     */
    public function dispatch(object $query)
    {
        return $this->handle($query);
    }
}
