<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\Fixtures;

use DevTools\Messenger\CommandBus;
use DevTools\UnitTest\Mock\CommandBus as TestCommandBus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Hautelook\AliceBundle\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait BaseDatabaseTrait
{
    /**
     * The name of the Doctrine manager to use.
     */
    protected static ?string $manager = null;

    /**
     * @var string[] The list of bundles where to look for fixtures
     */
    protected static array $bundles = [];

    /**
     * Append fixtures instead of purging.
     */
    protected static bool $append = false;

    /**
     * Use TRUNCATE to purge.
     */
    protected static bool $purgeWithTruncate = false;

    /**
     * The name of the Doctrine shard to use.
     */
    protected static ?string $shard = null;

    /**
     * The name of the Doctrine connection to use.
     */
    protected static ?string $connection = null;

    /**
     * Contain loaded fixture from alice.
     */
    protected static array $fixtures = [];

    protected static function ensureKernelTestCase(): void
    {
        if (!is_a(static::class, KernelTestCase::class, true)) {
            throw new \LogicException(sprintf(
                'The test class must extend "%s" to use "%s".',
                KernelTestCase::class,
                static::class
            ));
        }
    }

    protected static function populateDatabase(): void
    {
        /** @var ContainerInterface $container */
        $container = static::getContainer();

        /** @var TestCommandBus $commandBus */
        $commandBus = $container->get(CommandBus::class);
        /** @var TestCaseFixturesLocator $fixtureLocator */
        $fixtureLocator = $container->get(TestCaseFixturesLocator::class);
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = $container->get('hautelook_alice.loader');
        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $doctrine->getManager(static::$manager);

        $fixtureLocator->setFixturesDirectories(static::getContextualFixtures());
        $commandBus->disable();

        try {
            static::$fixtures = $fixtureLoader->load(
                new Application(static::$kernel), // OK this is ugly... But there is no other way without redesigning LoaderInterface from the ground.
                $entityManager,
                static::$bundles,
                static::$kernel->getEnvironment(),
                static::$append,
                static::$purgeWithTruncate,
                static::$shard
            );
        } finally {
            $fixtureLocator->setFixturesDirectories([]);

            $commandBus->clear();
            $commandBus->enable();
        }
    }

    /**
     * @return string[]
     */
    protected static function getContextualFixtures(): array
    {
        return [];
    }
}
