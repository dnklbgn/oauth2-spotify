<?php

declare(strict_types=1);

namespace Dnklbgn\OAuth2\Client\Provider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class SpotifyIdentityProviderException extends IdentityProviderException
{
    /**
     * Creates oauth exception from response
     *
     * @param ResponseInterface $response
     * @param array|string $data
     *
     * @return static
     */
    public static function errorResponse(ResponseInterface $response, array|string $data): static
    {
        $message = $response->getReasonPhrase();
        $code = $response->getStatusCode();
        $body = (string)$response->getBody();

        if (isset($data['error_description'])) {
            $message = (string)$data['error_description'];
        }

        if (isset($data['error']) && is_array($data['error'])) {
            if (isset($data['error']['message'])) {
                $message = (string)$data['error']['message'];
            }

            if (isset($data['error']['status'])) {
                $code = (int)$data['error']['status'];
            }
        }

        return new static($message, $code, $body);
    }
}
