<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait ServiceAccessTrait
{
    protected function getService(string $id): ?object
    {
        if (!method_exists($this, 'getContainer')) {
            static::fail(sprintf(
                'The test class must extend "%s" for the access to the service container.',
                KernelTestCase::class
            ));
        }

        return self::getContainer()->get($id);
    }
}
