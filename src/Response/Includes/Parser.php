<?php

declare(strict_types = 1);

namespace DevTools\Response\Includes;

class Parser
{
    public function parse(array $includes, int $maxLevel): array
    {
        $result = [];

        foreach ($includes as $include) {
            $level = 0;
            $tokens = explode('.', is_string($include) ? $include : '');

            if (empty($tokens)) {
                continue;
            }

            $rootToken = $tokens[0];

            if (!isset($result[$rootToken])) {
                $result[$rootToken] = [];
            }

            $lastItem = &$result[$rootToken];
            array_shift($tokens);

            foreach ($tokens as $token) {
                ++$level;

                if ($level === $maxLevel) {
                    break;
                }

                if (!isset($lastItem[$token])) {
                    $lastItem[$token] = [];
                }

                $lastItem = &$lastItem[$token];
            }
        }

        return $result;
    }
}
