<?php

declare(strict_types = 1);

namespace DevTools\Repository;

use DevTools\Domain\AbstractAggregateRoot;
use DevTools\Messenger\EventBus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    private ?EventBus $eventBus;

    public function __construct(ManagerRegistry $registry, string $entityClass, EventBus $eventBus = null)
    {
        parent::__construct($registry, $entityClass);

        $this->eventBus = $eventBus;
    }

    public function save(object ...$entities): void
    {
        $this->_em->transactional(function () use ($entities): void {
            foreach ($entities as $entity) {
                $this->_em->persist($entity);
            }

            $this->_em->flush();

            $this->publishEvents($entities);
        });
    }

    public function remove(object ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->_em->remove($entity);
        }

        $this->_em->flush();
    }

    protected function publishEvents(array $entities): void
    {
        if (null === $this->eventBus) {
            return;
        }

        foreach ($entities as $entity) {
            if (!$entity instanceof AbstractAggregateRoot) {
                continue;
            }

            $events = $entity->popRecordedEvents();

            foreach ($events as $event) {
                $this->eventBus->dispatch($event);
            }
        }
    }
}
