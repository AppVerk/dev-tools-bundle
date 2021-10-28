<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\Fixtures;

trait ReloadDatabaseTrait
{
    use BaseDatabaseTrait;

    protected static function bootKernel(array $options = [])
    {
        static::ensureKernelTestCase();
        $kernel = parent::bootKernel($options);
        static::populateDatabase();

        return $kernel;
    }
}
