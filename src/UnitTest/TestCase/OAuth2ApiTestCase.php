<?php

declare(strict_types = 1);

namespace DevTools\UnitTest\TestCase;

use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Trikoder\Bundle\OAuth2Bundle\Manager\ClientManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;

abstract class OAuth2ApiTestCase extends ApiTestCase
{
    protected const DEFAULT_REQUEST_HEADERS = [
        'CONTENT_TYPE' => 'application/json',
    ];

    protected const DEFAULT_USER_NAME = 'tester@appverk.com';

    protected const DEFAULT_CLIENT_ID = '9b7f6ace2cd28c3d15f1d810bce6e589';

    protected const ACCESS_TOKEN_TTL = 3600;

    protected const PRIVATE_KEY_PATH = '/config/oauth/private.key';

    protected function generateAccessTokenForUser(string $username, string $clientId = null, array $scopes = []): string
    {
        return $this->generateOAuthAccessToken($username, $clientId, $scopes);
    }

    protected function generateAccessTokenForClient(string $clientId = null, array $scopes = []): string
    {
        return $this->generateOAuthAccessToken(null, $clientId, $scopes);
    }

    protected function makeRequest(string $method, string $url, array $data = [], array $headers = []): Response
    {
        $allHeaders = array_merge(self::DEFAULT_REQUEST_HEADERS, $headers);

        $this->client->request($method, $url, [], [], $allHeaders, (string) json_encode($data));

        return $this->client->getResponse();
    }

    protected function makeAuthorizedRequest(
        string $method,
        string $url,
        string $token = null,
        array $data = [],
        array $headers = []
    ): Response {
        if (null === $token) {
            $token = $this->generateAccessTokenForUser(self::DEFAULT_USER_NAME);
        }

        $allHeaders = array_merge(self::DEFAULT_REQUEST_HEADERS, $headers);
        $allHeaders['HTTP_AUTHORIZATION'] = "Bearer {$token}";

        $this->client->request($method, $url, [], [], $allHeaders, (string) json_encode($data));

        return $this->client->getResponse();
    }

    protected function initClient(string $clientId): void
    {
        /** @var ClientManagerInterface $clientManager */
        $clientManager = self::$container->get(ClientManagerInterface::class);

        $client = $clientManager->find($clientId);

        if (null !== $client) {
            return;
        }

        $client = new Client($clientId, hash('sha512', random_bytes(32)));
        $client->setActive(true);

        $clientManager->save($client);
    }

    private function generateOAuthAccessToken(
        string $userIdentifier = null,
        string $clientId = null,
        array $scopes = []
    ): string {
        /** @var ClientRepositoryInterface $clientRepository */
        $clientRepository = self::$container->get(ClientRepositoryInterface::class);
        /** @var AccessTokenRepositoryInterface $tokenRepository */
        $tokenRepository = self::$container->get(AccessTokenRepositoryInterface::class);

        $clientId = $clientId ?? self::DEFAULT_CLIENT_ID;
        $privateKey = new CryptKey($this->getRootDir() . self::PRIVATE_KEY_PATH, null, false);

        $this->initClient($clientId);

        $client = $clientRepository->getClientEntity($clientId);

        $accessToken = $tokenRepository->getNewToken($client, $scopes, $userIdentifier);
        $accessToken->setExpiryDateTime((new \DateTimeImmutable())->add(new \DateInterval('PT1H')));
        $accessToken->setPrivateKey($privateKey);
        $accessToken->setIdentifier((string) Uuid::v4());

        $tokenRepository->persistNewAccessToken($accessToken);

        return (string) $accessToken;
    }
}
