<?php

declare(strict_types=1);

namespace Dnklbgn\OAuth2\Client\Provider;

use Dnklbgn\OAuth2\Client\Provider\Exception\SpotifyIdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Spotify extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string Default authorization host
     */
    protected string $authHost = 'https://accounts.spotify.com';

    /**
     * @var string Default API host
     */
    protected string $apiHost = 'https://api.spotify.com';

    /**
     * Returns the base URL for authorizing a client
     *
     * @return string
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->authHost . '/authorize';
    }

    /**
     * Returns the base URL for requesting an access token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->authHost . '/api/token';
    }

    /**
     * Returns the URL for requesting the resource owner's details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->apiHost . '/v1/me';
    }

    /**
     * Returns the default scopes used by this provider
     *
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * Checks a provider response for errors
     *
     * @param ResponseInterface $response
     * @param array|string $data
     *
     * @return void
     *
     * @throws SpotifyIdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw SpotifyIdentityProviderException::errorResponse($response, $data);
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner details request
     *
     * @param array $response
     * @param AccessToken $token
     *
     * @return SpotifyResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token): SpotifyResourceOwner
    {
        return new SpotifyResourceOwner($response);
    }
}
