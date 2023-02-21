<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;

abstract class AbstractApiClient implements ApiClientInterface
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var mixed[]
     */
    protected $clientScope = [];

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getClientScope(): AccessScope
    {
        return new AccessScope($this->clientScope);
    }

    /**
     * @param mixed[] $clientScope
     */
    public function setClientScope(array $clientScope): void
    {
        $this->clientScope = $clientScope;
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
