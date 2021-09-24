<?php

declare(strict_types = 1);

namespace DevTools\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class AbstractVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;
        $context = new Context($token);

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = self::ACCESS_DENIED;

            if ($this->voteOnAttribute($attribute, $subject, $context)) {
                // grant access as soon as at least one attribute returns a positive response
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    /**
     * @param mixed $subject
     */
    abstract protected function supports(string $attribute, $subject): bool;

    /**
     * @param mixed $subject
     */
    abstract protected function voteOnAttribute(string $attribute, $subject, Context $context): bool;
}
