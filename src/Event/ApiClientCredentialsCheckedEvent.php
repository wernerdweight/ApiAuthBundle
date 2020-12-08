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
     */
    public function __construct(ApiClientCredentials $credentials, ApiClientInterface $apiClient)
    {
        $this->credentials = $credentials;
        $this->apiClient = $apiClient;
    }

    public function getCredentials(): ApiClientCredentials
    {
        return $this->credentials;
    }

    public function getApiClient(): ApiClientInterface
    {
        return $this->apiClient;
    }

    /**
     * @return ApiClientCredentialsCheckedEvent
     */
    public function setValid(bool $valid): self
    {
        $this->valid = $valid;
        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }
}
