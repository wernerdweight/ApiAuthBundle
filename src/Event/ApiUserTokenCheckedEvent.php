<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

class ApiUserTokenCheckedEvent extends Event
{
    /** @var string */
    public const NAME = 'wds.api_auth_bundle.api_user_token_checked';

    /** @var bool */
    private $valid = true;

    /** @var string */
    private $token;

    /** @var ApiUserInterface */
    private $apiUser;

    /**
     * ApiUserTokenCheckedEvent constructor.
     *
     * @param string           $token
     * @param ApiUserInterface $apiUser
     */
    public function __construct(string $token, ApiUserInterface $apiUser)
    {
        $this->token = $token;
        $this->apiUser = $apiUser;
    }

    /**
     * @return string
     */
    public function getToken(): string
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

    /**
     * @param bool $valid
     *
     * @return ApiUserTokenCheckedEvent
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
