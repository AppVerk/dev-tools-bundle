<?php

declare(strict_types = 1);

namespace DevTools\Messenger\StreamWorkflow;

use DevTools\Messenger\Exception\UnprocessedPositionException;
use DevTools\Messenger\Stamp\StreamWorkflowStamp;

class StreamWorkflow
{
    private const PROCESSED_COMMAND_STATUS = 'processed';

    private const PENDING_COMMAND_STATUS = 'pending';

    private StorageInterface $storage;

    private Lock $lock;

    public function __construct(StorageInterface $storage, Lock $lock)
    {
        $this->storage = $storage;
        $this->lock = $lock;
    }

    /**
     * @return mixed
     */
    public function addItem(StreamWorkflowStamp $workflowStamp, callable $processor)
    {
        $this->lock->acquire($workflowStamp->getNamespace());

        $streamPosition = $this->storage->getCurrentPosition(
            $workflowStamp->getNamespace(),
            $workflowStamp->getWorkflowId()
        );

        if (null !== $streamPosition && $streamPosition >= $workflowStamp->getCurrentPosition()) {
            throw new \LogicException(sprintf(
                'Stream has greater or equal version to adding item in workflow "%s", "%s"',
                $workflowStamp->getNamespace(),
                $workflowStamp->getWorkflowId()
            ));
        }

        $this->storage->addItem(
            $workflowStamp->getNamespace(),
            $workflowStamp->getWorkflowId(),
            $workflowStamp->getCurrentPosition(),
            self::PENDING_COMMAND_STATUS
        );

        try {
            $newStamp = null === $streamPosition ? null : $workflowStamp->withRequiredPosition($streamPosition);

            return $processor($newStamp);
        } catch (\Throwable $exception) {
            $this->storage->removeItem(
                $workflowStamp->getNamespace(),
                $workflowStamp->getWorkflowId(),
                $workflowStamp->getCurrentPosition()
            );

            throw $exception;
        }
    }

    /**
     * @return mixed
     */
    public function processItem(StreamWorkflowStamp $workflowStamp, callable $processor)
    {
        $requiredPosition = $workflowStamp->getRequiredPosition();

        if (null === $requiredPosition) {
            $result = $processor();

            $this->storage->setItemStatus(
                $workflowStamp->getNamespace(),
                $workflowStamp->getWorkflowId(),
                $workflowStamp->getCurrentPosition(),
                self::PROCESSED_COMMAND_STATUS
            );

            return $result;
        }

        $requiredItemStatus = $this->storage->getItemStatus(
            $workflowStamp->getNamespace(),
            $workflowStamp->getWorkflowId(),
            $requiredPosition
        );

        if (self::PROCESSED_COMMAND_STATUS === $requiredItemStatus) {
            $result = $processor();

            $this->storage->setItemStatus(
                $workflowStamp->getNamespace(),
                $workflowStamp->getWorkflowId(),
                $workflowStamp->getCurrentPosition(),
                self::PROCESSED_COMMAND_STATUS
            );
            $this->storage->removeItem(
                $workflowStamp->getNamespace(),
                $workflowStamp->getWorkflowId(),
                $requiredPosition,
            );

            return $result;
        }

        throw new UnprocessedPositionException(sprintf(
            'Item on position "%d" not processed. Required by item "%d" of workflow "%s" in namespace "%s".',
            $requiredPosition,
            $workflowStamp->getCurrentPosition(),
            $workflowStamp->getWorkflowId(),
            $workflowStamp->getNamespace()
        ));
    }
}
