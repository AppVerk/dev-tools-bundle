<?php

declare(strict_types = 1);

namespace DevTools\Repository\Utils;

class TokenGenerator implements TokenGeneratorInterface
{
    private int $entropy;

    public function __construct(int $entropy = 256)
    {
        $this->entropy = $entropy;
    }

    public function generateToken(): string
    {
        $bytes = random_bytes($this->entropy / 8);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
