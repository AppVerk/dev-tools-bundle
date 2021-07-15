<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Serialization;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class SharedTransportSerializer implements SerializerInterface
{
    private SerializerInterface $originalSerializer;

    private string $busName;

    private string $busNameHeader;

    private HeadersConverterInterface $headersConverter;

    public function __construct(
        string $busName,
        SerializerInterface $originalSerializer,
        HeadersConverterInterface $headersConverter
    ) {
        $this->busName = $busName;
        $this->originalSerializer = $originalSerializer;
        $this->headersConverter = $headersConverter;
        $this->busNameHeader = HeadersConverterInterface::STAMP_HEADER_PREFIX . BusNameStamp::class;
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $busNameStamp = $encodedEnvelope['headers'][$this->busNameHeader] ?? null;

        if (null !== $busNameStamp) {
            return $this->originalSerializer->decode($encodedEnvelope);
        }

        $encodedEnvelope['headers'] = $this->headersConverter->fromSharedFormat($encodedEnvelope);

        return $this->originalSerializer
            ->decode($encodedEnvelope)
            ->with(new BusNameStamp($this->busName))
        ;
    }

    public function encode(Envelope $envelope): array
    {
        $result = $this->originalSerializer->encode($envelope);

        if ($envelope->last(ReceivedStamp::class)) {
            return $result;
        }

        return [
            'body' => $result['body'],
            'headers' => $this->headersConverter->toSharedFormat($result),
        ];
    }
}
