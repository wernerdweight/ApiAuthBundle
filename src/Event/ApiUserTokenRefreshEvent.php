<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

class ApiUserTokenRefreshEvent extends Event
{
    /** @var string */
    public const NAME = 'wds.api_auth_bundle.api_user_token_refresh';

    /** @var string|null */
    private $token;

    /** @var ApiUserInterface */
    private $apiUser;

    /**
     * ApiUserTokenRefreshEvent constructor.
     *
     * @param ApiUserInterface $apiUser
     */
    public function __construct(ApiUserInterface $apiUser)
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return ApiUserInterface
     */
    public function getApiUser(): ApiUserInterface
    {
        return $this->apiUser;
    }
}
