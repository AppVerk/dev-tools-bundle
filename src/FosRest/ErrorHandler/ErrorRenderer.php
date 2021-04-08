<?php

declare(strict_types = 1);

namespace DevTools\FosRest\ErrorHandler;

use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ErrorRenderer implements ErrorRendererInterface
{
    /**
     * @var ErrorRendererInterface
     */
    private $decoratedRenderer;

    public function __construct(ErrorRendererInterface $decoratedRenderer)
    {
        $this->decoratedRenderer = $decoratedRenderer;
    }

    public function render(\Throwable $exception): FlattenException
    {
        if ($exception instanceof HandlerFailedException && null !== $exception->getPrevious()) {
            return $this->decoratedRenderer->render($exception->getPrevious());
        }

        return $this->decoratedRenderer->render($exception);
    }
}
