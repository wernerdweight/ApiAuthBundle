<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;

abstract class AbstractApiClient implements ApiClientInterface
{
    /** @var string */
    protected $clientId;

    /** @var string */
    protected $clientSecret;

    /** @var array */
    protected $clientScope = [];

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     *
     * @return ApiClientInterface
     */
    public function setClientId(string $clientId): ApiClientInterface
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     *
     * @return ApiClientInterface
     */
    public function setClientSecret(string $clientSecret): ApiClientInterface
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @return AccessScope
     */
    public function getClientScope(): AccessScope
    {
        return new AccessScope($this->clientScope);
    }

    /**
     * @param array $clientScope
     *
     * @return ApiClientInterface
     */
    public function setClientScope(array $clientScope): ApiClientInterface
    {
        $this->clientScope = $clientScope;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        // ApiClient is not meant to use the default symfony role-based access control model
        return [];
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->getClientSecret();
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getClientId();
    }

    /**
     * ApiClient does not store any non-persistent sensitive information.
     */
    public function eraseCredentials(): void
    {
    }
}
