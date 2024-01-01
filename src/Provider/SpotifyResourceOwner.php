<?php

declare(strict_types=1);

namespace Dnklbgn\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class SpotifyResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * Raw response
     *
     * @var array
     */
    protected array $response;

    /**
     * Creates new response owner
     *
     * @param array $response
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * Get resource owner country
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->getValueByKey($this->response, 'country');
    }

    /**
     * Get resource owner display name
     *
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->getValueByKey($this->response, 'display_name');
    }

    /**
     * Get resource owner email address
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getValueByKey($this->response, 'email');
    }

    /**
     * Get resource owner explicit content settings
     *
     * @return array|null
     */
    public function getExplicitContent(): ?array
    {
        return $this->getValueByKey($this->response, 'explicit_content');
    }

    /**
     * Get resource owner known external URLs
     *
     * @return array|null
     */
    public function getExternalUrls(): ?array
    {
        return $this->getValueByKey($this->response, 'external_urls');
    }

    /**
     * Get information about resource owner followers
     *
     * @return array|null
     */
    public function getFollowers(): ?array
    {
        return $this->getValueByKey($this->response, 'followers');
    }

    /**
     * Get link for the Web API endpoint for this resource owner
     *
     * @return string|null
     */
    public function getHref(): ?string
    {
        return $this->getValueByKey($this->response, 'href');
    }

    /**
     * Get resource owner Spotify user ID
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getValueByKey($this->response, 'id');
    }

    /**
     * Get resource owner profile images
     *
     * @return array|null
     */
    public function getImages(): ?array
    {
        return $this->getValueByKey($this->response, 'images');
    }

    /**
     * Get resource owner Spotify subscription level
     *
     * @return string|null
     */
    public function getProduct(): ?string
    {
        return $this->getValueByKey($this->response, 'product');
    }

    /**
     * Get resource owner type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getValueByKey($this->response, 'type');
    }

    /**
     * Get resource owner Spotify URI
     *
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->getValueByKey($this->response, 'uri');
    }

    /**
     * Return all the owner details available as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
