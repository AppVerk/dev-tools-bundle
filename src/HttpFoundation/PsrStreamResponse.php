<?php

declare(strict_types = 1);

namespace DevTools\HttpFoundation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Response;

class PsrStreamResponse extends Response
{
    const BUFFER_SIZE = 4096;

    private int $bufferSize;

    private StreamInterface $streamContent;

    public function __construct(ResponseInterface $response, int $bufferSize = self::BUFFER_SIZE)
    {
        parent::__construct(null, $response->getStatusCode(), $response->getHeaders());

        $this->streamContent = $response->getBody();
        $this->bufferSize = $bufferSize;
    }

    /**
     * @return $this
     */
    public function sendContent()
    {
        $chunked = $this->headers->has('Transfer-Encoding');
        $this->streamContent->seek(0);

        for (;;) {
            $chunk = $this->streamContent->read($this->bufferSize);

            if ($chunked) {
                echo sprintf("%x\r\n", mb_strlen($chunk));
            }

            echo $chunk;

            if ($chunked) {
                echo "\r\n";
            }

            flush();

            if (!$chunk) {
                return $this;
            }
        }

        return $this;
    }

    public function getContent()
    {
        return false;
    }
}
