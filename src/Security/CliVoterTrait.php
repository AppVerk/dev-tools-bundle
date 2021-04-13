<?php

declare(strict_types = 1);

namespace DevTools\Security;

trait CliVoterTrait
{
    public function isRunningInCli(): bool
    {
        return \PHP_SAPI === 'cli';
    }
}
