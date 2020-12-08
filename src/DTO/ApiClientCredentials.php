<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\DTO;

class ApiClientCredentials
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /**
     * ApiClientCredentials constructor.
     */
    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}
