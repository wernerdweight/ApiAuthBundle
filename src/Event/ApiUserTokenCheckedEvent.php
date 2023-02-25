<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

class ApiUserTokenCheckedEvent extends Event
{
    /**
     * @var string
     */
    public const NAME = 'wds.api_auth_bundle.api_user_token_checked';

    /**
     * @var bool
     */
    private $valid = true;

    /**
     * @var string
     */
    private $token;

    /**
     * @var ApiUserInterface
     */
    private $apiUser;

    public function __construct(string $token, ApiUserInterface $apiUser)
    {
        $this->token = $token;
        $this->apiUser = $apiUser;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getApiUser(): ApiUserInterface
    {
        return $this->apiUser;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }
}
