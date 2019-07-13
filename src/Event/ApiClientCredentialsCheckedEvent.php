<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;

class ApiClientCredentialsCheckedEvent extends Event
{
    /** @var string */
    public const NAME = 'wds.api_auth_bundle.api_client_credentials_checked';

    /** @var bool */
    private $valid = true;

    /** @var ApiClientCredentials */
    private $credentials;

    /** @var ApiClientInterface */
    private $apiClient;

    /**
     * ApiClientCredentialsCheckedEvent constructor.
     *
     * @param ApiClientCredentials $credentials
     * @param ApiClientInterface   $apiClient
     */
    public function __construct(ApiClientCredentials $credentials, ApiClientInterface $apiClient)
    {
        $this->credentials = $credentials;
        $this->apiClient = $apiClient;
    }

    /**
     * @return ApiClientCredentials
     */
    public function getCredentials(): ApiClientCredentials
    {
        return $this->credentials;
    }

    /**
     * @return ApiClientInterface
     */
    public function getApiClient(): ApiClientInterface
    {
        return $this->apiClient;
    }

    /**
     * @param bool $valid
     *
     * @return ApiClientCredentialsCheckedEvent
     */
    public function setValid(bool $valid): self
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }
}
