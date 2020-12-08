<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service\Event;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Event\ApiClientCredentialsCheckedEvent;
use WernerDweight\ApiAuthBundle\Event\ApiUserTokenCheckedEvent;

class ApiAuthEventDispatcher
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * ApiAuthEventDispatcher constructor.
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchTokenChecked(string $token, ApiUserInterface $user): ApiUserTokenCheckedEvent
    {
        /** @var ApiUserTokenCheckedEvent $event */
        $event = $this->eventDispatcher->dispatch(new ApiUserTokenCheckedEvent($token, $user));
        return $event;
    }

    public function dispatchCredentialsChecked(
        ApiClientCredentials $credentials,
        ApiClientInterface $apiClient
    ): ApiClientCredentialsCheckedEvent {
        /** @var ApiClientCredentialsCheckedEvent $event */
        $event = $this->eventDispatcher->dispatch(new ApiClientCredentialsCheckedEvent($credentials, $apiClient));
        return $event;
    }
}
