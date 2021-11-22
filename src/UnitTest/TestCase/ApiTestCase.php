<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\TestCase;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use DevTools\UnitTest\Fixtures\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{
    use PHPMatcherAssertions;
    use ReloadDatabaseTrait;
    use ServiceAccessTrait;

    protected KernelBrowser $client;

    protected string $responsesPath = '/tests/Functional';

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    protected function buildQuery(string $url, array $parameters): string
    {
        return $url . '?' . http_build_query($parameters);
    }

    protected function getRootDir(): string
    {
        return $this->getService('kernel')->getProjectDir();
    }

    protected function assertHeader(Response $response, string $contentType): void
    {
        $this->assertSame($response->headers->get('Content-Type'), $contentType);
    }

    protected function assertResponseContent(string $actualResponse, ?string $expectedContentFile): void
    {
        $expectedContentFilePath = null === $expectedContentFile
            ? null
            : $this->getRootDir() . $this->responsesPath . '/' . $expectedContentFile;

        $expectedResponse = null === $expectedContentFilePath ? '' : file_get_contents($expectedContentFilePath);

        $this->assertMatchesPattern($expectedResponse, $actualResponse);
    }

    protected function assertResponse(Response $response, string $expectedContentFile = null, int $statusCode = 200): void
    {
        $this->assertResponseCode($response, $statusCode);

        if (Response::HTTP_NO_CONTENT !== $statusCode) {
            $this->assertResponseContent($this->prettifyJson((string) $response->getContent()), $expectedContentFile);
        }
    }

    protected function assertResponseCode(Response $response, int $statusCode): void
    {
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    protected function prettifyJson(string $content): string
    {
        return (string) \json_encode(json_decode($content), \JSON_PRETTY_PRINT);
    }

    protected function loadJsonData(string $file): array
    {
        $content = file_get_contents($file);

        if (false === $content) {
            throw new \InvalidArgumentException(sprintf('Failed to load "%s" file.', $file));
        }

        return \json_decode($content, true);
    }
}
