<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

class ApiUserAuthenticatedEvent extends Event
{
    /** @var string */
    public const NAME = 'wds.api_auth_bundle.api_user_authenticated';

    /** @var ApiUserInterface */
    private $apiUser;

    /**
     * ApiUserAuthenticatedEvent constructor.
     * @param ApiUserInterface $apiUser
     */
    public function __construct(ApiUserInterface $apiUser)
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @return ApiUserInterface
     */
    public function getApiUser(): ApiUserInterface
    {
        return $this->apiUser;
    }
}
