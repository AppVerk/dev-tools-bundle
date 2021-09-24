<?php
declare(strict_types=1);

namespace DevTools\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Context
{
    private TokenInterface $token;

    public function __construct(TokenInterface $token)
    {
        $this->token = $token;
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    /**
     * @return string|\Stringable|UserInterface
     */
    public function getUser()
    {
        return $this->token->getUser();
    }

    public function isSystem(): bool
    {
        return \PHP_SAPI === 'cli' && $this->token instanceof NullToken;
    }

    public function hasRole(string ...$roles): bool
    {
        foreach ($roles as $role) {
            if (in_array($role, $this->token->getRoleNames())) {
                return true;
            }
        }

        return false;
    }
}