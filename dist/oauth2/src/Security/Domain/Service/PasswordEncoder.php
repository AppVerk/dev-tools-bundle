<?php

declare(strict_types = 1);

namespace App\Security\Domain\Service;

use App\Security\Domain\Model\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class PasswordEncoder
{
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function encode(string $password): string
    {
        $encoder = $this->encoderFactory->getEncoder(User::class);

        return $encoder->encodePassword($password, null);
    }

    public function isPasswordValid(?string $hashedPassword, string $plainPassword): bool
    {
        if (null === $hashedPassword) {
            return false;
        }

        $encoder = $this->encoderFactory->getEncoder(User::class);

        return $encoder->isPasswordValid($hashedPassword, $plainPassword, null);
    }
}
