<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use App\Security\Domain\Service\PasswordEncoder;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;

class OAuthUserResolveListener
{
    private UserProviderInterface $userProvider;

    private PasswordEncoder $passwordEncoder;

    public function __construct(UserProviderInterface $userProvider, PasswordEncoder $passwordEncoder)
    {
        $this->userProvider = $userProvider;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function onUserResolve(UserResolveEvent $event): void
    {
        try {
            $user = $this->userProvider->loadUserByUsername($event->getUsername());
        } catch (UsernameNotFoundException $exception) {
            return;
        }

        if (!$this->passwordEncoder->isPasswordValid($user->getPassword(), $event->getPassword())) {
            return;
        }

        $event->setUser($user);
    }
}