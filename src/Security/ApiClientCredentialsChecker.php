<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\AccessScopeCheckerFactory;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;
use WernerDweight\ApiAuthBundle\Service\Event\ApiAuthEventDispatcher;

class ApiClientCredentialsChecker
{
    /** @var string */
    private const EXCEPTION_NO_USER_TOKEN =
        'No user token present in headers! You must provide value for the header %s.';
    /** @var string */
    private const AUTH_ROUTE_NAME = 'wds_api_auth_authenticate';

    /** @var ApiAuthEventDispatcher */
    private $eventDispatcher;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var AccessScopeCheckerFactory */
    private $accessScopeCheckerFactory;

    /** @var ApiClientAuthenticatorRequestResolver */
    private $apiClientAuthenticatorRequestResolver;

    /** @var ApiUserTokenChecker */
    private $apiUserTokenChecker;

    /**
     * ApiClientAuthenticator constructor.
     */
    public function __construct(
        ApiAuthEventDispatcher $eventDispatcher,
        ConfigurationProvider $configurationProvider,
        AccessScopeCheckerFactory $accessScopeCheckerFactory,
        ApiClientAuthenticatorRequestResolver $apiClientAuthenticatorRequestResolver,
        ApiUserTokenChecker $apiUserTokenChecker
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationProvider = $configurationProvider;
        $this->accessScopeCheckerFactory = $accessScopeCheckerFactory;
        $this->apiClientAuthenticatorRequestResolver = $apiClientAuthenticatorRequestResolver;
        $this->apiUserTokenChecker = $apiUserTokenChecker;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     * @throws \WernerDweight\RA\Exception\RAException
     */
    private function checkUserApiToken(): bool
    {
        $token = $this->apiClientAuthenticatorRequestResolver->getApiUserToken();
        if (null === $token) {
            throw new UnauthorizedHttpException(ApiAuthEnum::REALM, \Safe\sprintf(self::EXCEPTION_NO_USER_TOKEN, ApiAuthEnum::API_USER_TOKEN_HEADER));
        }

        return $this->apiUserTokenChecker->check($token);
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function check(ApiClientCredentials $credentials, ApiClientInterface $user): bool
    {
        if ($user->getClientSecret() !== $credentials->getClientSecret()) {
            return false;
        }

        // no more checks for login action
        if (self::AUTH_ROUTE_NAME === $this->apiClientAuthenticatorRequestResolver->getRoute()) {
            return true;
        }

        // check api client scope
        $scopeAccessibility = ApiAuthEnum::SCOPE_ACCESSIBILITY_ACCESSIBLE;
        if (true === $this->configurationProvider->getClientUseScopeAccessModel()) {
            $scopeAccessibility = $this->accessScopeCheckerFactory
                ->get($this->configurationProvider->getClientAccessScopeChecker())
                ->check($user->getClientScope());
            if (ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN === $scopeAccessibility) {
                return false;
            }
        }

        $event = $this->eventDispatcher->dispatchCredentialsChecked($credentials, $user);
        if (true !== $event->isValid()) {
            return false;
        }

        if (ApiAuthEnum::SCOPE_ACCESSIBILITY_ON_BEHALF === $scopeAccessibility) {
            return $this->checkUserApiToken();
        }

        return true;
    }
}
