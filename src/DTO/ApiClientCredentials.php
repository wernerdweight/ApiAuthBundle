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
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}
