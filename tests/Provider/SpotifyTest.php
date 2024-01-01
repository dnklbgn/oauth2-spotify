<?php

declare(strict_types=1);

namespace Dnklbgn\OAuth2\Client\Tests\Provider;

use Dnklbgn\OAuth2\Client\Provider\Exception\SpotifyIdentityProviderException;
use Dnklbgn\OAuth2\Client\Provider\Spotify;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Stream;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class SpotifyTest extends TestCase
{
    protected Spotify $provider;

    protected function setUp(): void
    {
        $this->provider = new Spotify([
            'clientId'     => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri'  => 'none',
        ]);
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();

        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        self::assertArrayHasKey('client_id', $query);
        self::assertArrayHasKey('redirect_uri', $query);
        self::assertArrayHasKey('state', $query);
        self::assertArrayHasKey('scope', $query);
        self::assertArrayHasKey('response_type', $query);
        self::assertNotNull($this->provider->getState());
    }

    public function testGetBaseAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);

        self::assertSame('/authorize', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl(): void
    {
        $params = [];

        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);

        self::assertSame('/api/token', $uri['path']);
    }

    public function testGetResourceOwnerDetailsUrl(): void
    {
        $accessToken = $this->createMock(AccessToken::class);

        $url = $this->provider->getResourceOwnerDetailsUrl($accessToken);
        $uri = parse_url($url);

        self::assertSame('/v1/me', $uri['path']);
    }

    public function testGetAccessToken(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn(
                new Stream(
                    fopen('data://text/plain, {"access_token": "mock_access_token", "expires_in": 3600}', 'r'),
                ),
            );
        $response->method('getHeader')->willReturn(['content-type' => 'json']);
        $response->method('getStatusCode')->willReturn(200);

        $client = $this->createMock(ClientInterface::class);
        $client->method('send')->willReturn($response);

        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        self::assertSame('mock_access_token', $token->getToken());
        self::assertLessThanOrEqual(time() + 3600, $token->getExpires());
        self::assertGreaterThanOrEqual(time(), $token->getExpires());
        self::assertNull($token->getRefreshToken());
        self::assertNull($token->getResourceOwnerId());
    }

    public function testGetResourceOwner(): void
    {
        $resourceOwner = json_decode(
            file_get_contents(__DIR__.'/../Mock/spotify_resource_owner.json'),
            true,
            512,
            \JSON_THROW_ON_ERROR,
        );

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn(
                new Stream(
                    fopen('data://text/plain, ' . json_encode($resourceOwner), 'r'),
                ),
            );
        $response->method('getHeader')->willReturn(['content-type' => 'json']);
        $response->method('getStatusCode')->willReturn(200);

        $client = $this->createMock(ClientInterface::class);
        $client->method('send')->willReturn($response);

        $this->provider->setHttpClient($client);

        $accessToken = $this->createMock(AccessToken::class);
        $resourceOwnerResponse = $this->provider->getResourceOwner($accessToken);

        self::assertSame($resourceOwner['country'], $resourceOwnerResponse->getCountry());
        self::assertSame($resourceOwner['display_name'], $resourceOwnerResponse->getDisplayName());
        self::assertSame($resourceOwner['email'], $resourceOwnerResponse->getEmail());
        self::assertSame($resourceOwner['explicit_content'], $resourceOwnerResponse->getExplicitContent());
        self::assertSame($resourceOwner['external_urls'], $resourceOwnerResponse->getExternalUrls());
        self::assertSame($resourceOwner['followers'], $resourceOwnerResponse->getFollowers());
        self::assertSame($resourceOwner['href'], $resourceOwnerResponse->getHref());
        self::assertSame($resourceOwner['id'], $resourceOwnerResponse->getId());
        self::assertSame($resourceOwner['images'], $resourceOwnerResponse->getImages());
        self::assertSame($resourceOwner['product'], $resourceOwnerResponse->getProduct());
        self::assertSame($resourceOwner['type'], $resourceOwnerResponse->getType());
        self::assertSame($resourceOwner['uri'], $resourceOwnerResponse->getUri());
        self::assertSame($resourceOwner, $resourceOwnerResponse->toArray());
    }

    public function testCheckResponseForAuthenticationError(): void
    {
        $this->expectException(SpotifyIdentityProviderException::class);
        $this->expectExceptionMessage('Invalid client secret');
        $this->expectExceptionCode(401);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn(
                new Stream(
                    fopen(
                        'data://text/plain, {"error": "invalid_client", "error_description": "Invalid client secret"}',
                        'r',
                    ),
                ),
            );
        $response->method('getHeader')->willReturn(['content-type' => 'json']);
        $response->method('getStatusCode')->willReturn(401);

        $client = $this->createMock(ClientInterface::class);
        $client->method('send')->willReturn($response);

        $this->provider->setHttpClient($client);

        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }

    public function testCheckResponseForRegularError(): void
    {
        $this->expectException(SpotifyIdentityProviderException::class);
        $this->expectExceptionMessage('invalid id');
        $this->expectExceptionCode(400);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn(
                new Stream(
                    fopen('data://text/plain, {"error": {"status": 400, "message": "invalid id"}}', 'r'),
                ),
            );
        $response->method('getHeader')->willReturn(['content-type' => 'json']);
        $response->method('getStatusCode')->willReturn(401);

        $client = $this->createMock(ClientInterface::class);
        $client->method('send')->willReturn($response);

        $this->provider->setHttpClient($client);

        $accessToken = $this->createMock(AccessToken::class);
        $this->provider->getResourceOwner($accessToken);
    }
}
