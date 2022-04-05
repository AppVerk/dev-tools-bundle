<?php

declare(strict_types = 1);

namespace DevTools\FosRest\EventListener;

use FOS\RestBundle\EventListener\ResponseStatusCodeListener as DecoratedListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ResponseStatusCodeListener implements EventSubscriberInterface
{
    private DecoratedListener $decoratedListener;

    public function __construct(DecoratedListener $decoratedListener)
    {
        $this->decoratedListener = $decoratedListener;
    }

    public static function getSubscribedEvents(): array
    {
        return DecoratedListener::getSubscribedEvents();
    }

    public function getResponseStatusCodeFromThrowable(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HandlerFailedException && null !== $exception->getPrevious()) {
            $newEvent = clone $event;
            $newEvent->setThrowable($exception->getPrevious());

            $this->decoratedListener->getResponseStatusCodeFromThrowable($newEvent);

            return;
        }

        $this->decoratedListener->getResponseStatusCodeFromThrowable($event);
    }

    public function setResponseStatusCode(ResponseEvent $event): void
    {
        $this->decoratedListener->setResponseStatusCode($event);
    }
}
