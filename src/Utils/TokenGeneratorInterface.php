<?php

declare(strict_types = 1);

namespace DevTools\Repository\Utils;

interface TokenGeneratorInterface
{
    public function generateToken(): string;
}
