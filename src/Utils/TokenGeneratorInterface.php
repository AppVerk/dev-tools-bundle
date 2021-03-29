<?php

declare(strict_types = 1);

namespace DevTools\Utils;

interface TokenGeneratorInterface
{
    public function generateToken(): string;
}
