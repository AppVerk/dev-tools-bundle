<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Middleware;

use DevTools\Messenger\Stamp\StreamWorkflowStamp;
use DevTools\Messenger\StreamWorkflow\StreamWorkflow;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class StreamWorkflowMiddleware implements MiddlewareInterface
{
    private StreamWorkflow $workflow;

    public function __construct(StreamWorkflow $workflow)
    {
        $this->workflow = $workflow;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        /** @var null|StreamWorkflowStamp $workflowStamp */
        $workflowStamp = $envelope->last(StreamWorkflowStamp::class);
        /** @var null|ReceivedStamp $receivedStamp */
        $receivedStamp = $envelope->last(ReceivedStamp::class);

        if (null !== $workflowStamp) {
            if (null !== $receivedStamp) {
                $result = $this->workflow->processItem(
                    $workflowStamp,
                    function () use ($stack, $envelope) {
                        return $this->next($envelope, $stack);
                    }
                );

                return $result ?? $envelope;
            }

            return $this->workflow->addItem(
                $workflowStamp,
                function (?StreamWorkflowStamp $newStamp) use ($stack, $envelope) {
                    $newEnvelope = null === $newStamp
                        ? $envelope
                        : $envelope->withoutAll(StreamWorkflowStamp::class)->with($newStamp)
                    ;

                    return $this->next($newEnvelope, $stack);
                }
            );
        }

        return $this->next($envelope, $stack);
    }

    private function next(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $stack->next()->handle($envelope, $stack);
    }
}
