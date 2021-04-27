<?php

declare(strict_types = 1);

namespace DevTools\Security;

use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

trait CliVoterTrait
{
    public function isRunningInCli(TokenInterface $token): bool
    {
        return \PHP_SAPI === 'cli' && $token instanceof NullToken;
    }
}
