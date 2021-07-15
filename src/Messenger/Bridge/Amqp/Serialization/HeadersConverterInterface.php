<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Serialization;

interface HeadersConverterInterface
{
    public const STAMP_HEADER_PREFIX = 'X-Message-Stamp-';

    public function toSharedFormat(array $encodedEnvelope): array;

    public function fromSharedFormat(array $encodedEnvelope): array;
}
