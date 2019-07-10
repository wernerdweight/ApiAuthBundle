<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

trait ApiClientTrait
{
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
