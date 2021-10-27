<?php

declare(strict_types = 1);

namespace DevTools\Messenger\StreamWorkflow;

use DevTools\Messenger\Exception\UnableToAcquireExclusivity;
use Symfony\Component\Lock\Lock as SymfonyLock;
use Symfony\Component\Lock\LockFactory;

class Lock
{
    private const LOCK_PREFIX = 'stream_workflow';

    private const DEFAULT_TTL = 60;

    private const CACHED_ITEMS_COUNT = 50;

    private ?int $ttl;

    private LockFactory $lockFactory;

    /**
     * @var SymfonyLock[]
     */
    private array $locks = [];

    public function __construct(LockFactory $lockFactory, int $ttl = null)
    {
        $this->lockFactory = $lockFactory;
        $this->ttl = $ttl ?? self::DEFAULT_TTL;
    }

    public function acquire(string $namespace): void
    {
        $lockKey = self::LOCK_PREFIX . ':' . $namespace;
        $lock = $this->locks[$lockKey] ?? null;

        if (null !== $lock && !$lock->isExpired()) {
            return;
        }

        $lock = $this->lockFactory->createLock($lockKey, $this->ttl);

        if ($lock->acquire()) {
            $this->locks[$lockKey] = $lock;

            if (count($this->locks) > self::CACHED_ITEMS_COUNT) {
                array_shift($this->locks);
            }

            return;
        }

        throw new UnableToAcquireExclusivity(sprintf(
            'Unable to acquire exclusivity for stream workflow in namespace "%s".',
            $namespace
        ));
    }
}
