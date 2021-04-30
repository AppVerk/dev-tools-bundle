<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\Fixtures;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use Hautelook\AliceBundle\Locator\EnvDirectoryLocator;
use Nelmio\Alice\IsAServiceTrait;

final class TestCaseFixturesLocator implements FixtureLocatorInterface
{
    use IsAServiceTrait;

    private FixtureLocatorInterface $decoratedFixtureLocator;

    /**
     * @var string[]
     */
    private array $fixturesDirectories = [];

    private string $fixturesLocation;

    public function __construct(FixtureLocatorInterface $decoratedFixtureLocator, string $fixturesLocation)
    {
        $this->decoratedFixtureLocator = $decoratedFixtureLocator;
        $this->fixturesLocation = $fixturesLocation;
    }

    /**
     * @param string[] $fixturesDirectories
     */
    public function setFixturesDirectories(array $fixturesDirectories): void
    {
        $this->fixturesDirectories = $fixturesDirectories;
    }

    /**
     * {@inheritdoc}
     */
    public function locateFiles(array $bundles, string $environment): array
    {
        $files = $this->decoratedFixtureLocator->locateFiles($bundles, $environment);

        $testCaseFiles = (new EnvDirectoryLocator($this->fixturesDirectories, [$this->fixturesLocation]))
            ->locateFiles([], '')
        ;

        return array_merge($files, $testCaseFiles);
    }
}
