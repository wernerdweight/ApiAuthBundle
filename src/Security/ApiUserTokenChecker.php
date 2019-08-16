<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\AccessScopeCheckerFactory;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;
use WernerDweight\ApiAuthBundle\Service\Event\ApiAuthEventDispatcher;

class ApiUserTokenChecker
{
    /** @var ApiAuthEventDispatcher */
    private $eventDispatcher;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var AccessScopeCheckerFactory */
    private $accessScopeCheckerFactory;

    /** @var ApiUserProvider */
    private $apiUserProvider;

    /** @var ApiUserInterface|null */
    private $apiUser;

    /**
     * ApiClientAuthenticator constructor.
     *
     * @param ApiAuthEventDispatcher    $eventDispatcher
     * @param ConfigurationProvider     $configurationProvider
     * @param AccessScopeCheckerFactory $accessScopeCheckerFactory
     * @param ApiUserProvider           $apiUserProvider
     */
    public function __construct(
        ApiAuthEventDispatcher $eventDispatcher,
        ConfigurationProvider $configurationProvider,
        AccessScopeCheckerFactory $accessScopeCheckerFactory,
        ApiUserProvider $apiUserProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationProvider = $configurationProvider;
        $this->accessScopeCheckerFactory = $accessScopeCheckerFactory;
        $this->apiUserProvider = $apiUserProvider;
    }

    /**
     * @return ApiUserInterface|null
     */
    public function getCheckedApiUser(): ?ApiUserInterface
    {
        return $this->apiUser;
    }

    /**
     * @return bool
     *
     * @throws \Safe\Exceptions\StringsException
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function check(string $token): bool
    {
        // authenticate user by api token
        $apiUser = $this->apiUserProvider->loadUserByUsername($token);

        // check api user scope
        if (true === $this->configurationProvider->getUserUseScopeAccessModel()) {
            $scopeAccessibility = $this->accessScopeCheckerFactory
                ->get($this->configurationProvider->getUserAccessScopeChecker())
                ->check($apiUser->getUserScope());
            if (ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN === $scopeAccessibility) {
                return false;
            }
        }

        $event = $this->eventDispatcher->dispatchTokenChecked($token, $apiUser);
        if (true !== $event->isValid()) {
            return false;
        }

        $this->apiUser = $apiUser;
        return true;
    }
}
