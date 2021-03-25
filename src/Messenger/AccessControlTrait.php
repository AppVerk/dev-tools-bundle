<?php

declare(strict_types = 1);

namespace DevTools\Messenger;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait AccessControlTrait
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker): void
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param mixed $attributes
     * @param mixed $subject
     *
     * @throws AccessDeniedException
     */
    protected function denyAccessUnlessGranted($attributes, $subject = null, string $message = 'Access Denied.'): void
    {
        if ($this->authorizationChecker->isGranted($attributes, $subject)) {
            return;
        }

        $exception = new AccessDeniedException($message);
        $exception->setAttributes($attributes);
        $exception->setSubject($subject);

        throw $exception;
    }
}
