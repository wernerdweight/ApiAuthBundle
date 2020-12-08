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

    /** @var mixed[] */
    protected $clientScope = [];

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): ApiClientInterface
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): ApiClientInterface
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function getClientScope(): AccessScope
    {
        return new AccessScope($this->clientScope);
    }

    /**
     * @param mixed[] $clientScope
     */
    public function setClientScope(array $clientScope): ApiClientInterface
    {
        $this->clientScope = $clientScope;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        // ApiClient is not meant to use the default symfony role-based access control model
        return [];
    }

    public function getPassword(): string
    {
        return $this->getClientSecret();
    }

    public function getSalt(): ?string
    {
        return null;
    }

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
