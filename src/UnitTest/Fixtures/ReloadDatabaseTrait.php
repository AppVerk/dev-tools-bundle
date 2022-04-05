<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\Fixtures;

use Symfony\Component\HttpKernel\KernelInterface;

trait ReloadDatabaseTrait
{
    use BaseDatabaseTrait;

    protected static function bootKernel(array $options = []): KernelInterface
    {
        static::ensureKernelTestCase();
        $kernel = parent::bootKernel($options);
        static::populateDatabase();

        return $kernel;
    }
}
