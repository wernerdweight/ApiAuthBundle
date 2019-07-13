<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Event\ApiClientCredentialsCheckedEvent;
use WernerDweight\ApiAuthBundle\Event\ApiUserTokenCheckedEvent;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\AccessScopeCheckerFactory;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;

final class ApiClientAuthenticator implements AuthenticatorInterface
{
    /** @var string */
    private const AUTHORIZATION_REQUIRED_MESSAGE = 'Client id and secret are required to authenticate!';
    /** @var string */
    private const UNAUTHORIZED_MESSAGE =
        'Client id, secret or user api token are invalid, expired or not allowed access!';
    /** @var string */
    private const EXCEPTION_NO_USER_TOKEN =
        'No user token present in headers! You must provide value for the header %s.';

    /** @var string|null */
    private $apiUserToken;

    /** @var ApiUserInterface|null */
    private $apiUser;

    /** @var Security */
    private $security;

    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var AccessScopeCheckerFactory */
    private $accessScopeCheckerFactory;

    /** @var ApiUserProvider */
    private $apiUserProvider;

    /**
     * ApiClientAuthenticator constructor.
     *
     * @param Security                  $security
     * @param EventDispatcher           $eventDispatcher
     * @param ConfigurationProvider     $configurationProvider
     * @param AccessScopeCheckerFactory $accessScopeCheckerFactory
     * @param ApiUserProvider           $apiUserProvider
     */
    public function __construct(
        Security $security,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationProvider $configurationProvider,
        AccessScopeCheckerFactory $accessScopeCheckerFactory,
        ApiUserProvider $apiUserProvider
    ) {
        $this->security = $security;
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationProvider = $configurationProvider;
        $this->accessScopeCheckerFactory = $accessScopeCheckerFactory;
        $this->apiUserProvider = $apiUserProvider;
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, ?AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(['message' => self::AUTHORIZATION_REQUIRED_MESSAGE], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        if (null !== $this->security->getUser()) {
            // already authenticated (not a stateless api)
            return false;
        }

        $headers = $request->headers;
        if (true !== $headers->has(ApiAuthEnum::CLIENT_ID_HEADER) ||
            true !== $headers->has(ApiAuthEnum::CLIENT_SECRET_HEADER)
        ) {
            return false;
        }

        if ($headers->has(ApiAuthEnum::API_USER_TOKEN_HEADER)) {
            /** @var string $token */
            $token = $headers->get(ApiAuthEnum::API_USER_TOKEN_HEADER);
            $this->apiUserToken = $token;
        }

        return true;
    }

    /**
     * @param Request $request
     *
     * @return ApiClientCredentials
     */
    public function getCredentials(Request $request): ApiClientCredentials
    {
        $headers = $request->headers;
        /** @var string $clientId */
        $clientId = $headers->get(ApiAuthEnum::CLIENT_ID_HEADER);
        /** @var string $clientSecret */
        $clientSecret = $headers->get(ApiAuthEnum::CLIENT_SECRET_HEADER);
        return new ApiClientCredentials($clientId, $clientSecret);
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return ApiClientInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        /** @var ApiClientInterface $apiClient */
        $apiClient = $userProvider->loadUserByUsername($credentials->getClientId());
        return $apiClient;
    }

    /**
     * @return bool
     *
     * @throws \Safe\Exceptions\StringsException
     * @throws \WernerDweight\RA\Exception\RAException
     */
    private function checkUserApiToken(): bool
    {
        if (null === $this->apiUserToken) {
            throw new UnauthorizedHttpException(
                'Basic realm="API"',
                \Safe\sprintf(self::EXCEPTION_NO_USER_TOKEN, ApiAuthEnum::API_USER_TOKEN_HEADER)
            );
        }

        // authenticate user by api token
        $apiUser = $this->apiUserProvider->loadUserByUsername($this->apiUserToken);

        // check api user scope
        if (true === $this->configurationProvider->getUserUseScopeAccessModel()) {
            $scopeAccessibility = $this->accessScopeCheckerFactory
                ->get($this->configurationProvider->getUserAccessScopeChecker())
                ->check($apiUser->getUserScope());
            if (ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN === $scopeAccessibility) {
                return false;
            }
        }

        // set the user as current user (instead of client)
        $this->apiUser = $apiUser;

        /** @var ApiUserTokenCheckedEvent $event */
        $event = $this->eventDispatcher->dispatch(new ApiUserTokenCheckedEvent($this->apiUserToken, $this->apiUser));
        if (true !== $event->isValid()) {
            return false;
        }

        return true;
    }

    /**
     * @param ApiClientCredentials $credentials
     * @param ApiClientInterface   $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($user->getClientSecret() !== $credentials->getClientSecret()) {
            return false;
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

        /** @var ApiClientCredentialsCheckedEvent $event */
        $event = $this->eventDispatcher->dispatch(new ApiClientCredentialsCheckedEvent($credentials, $user));
        if (true !== $event->isValid()) {
            return false;
        }

        if (ApiAuthEnum::SCOPE_ACCESSIBILITY_ON_BEHALF === $scopeAccessibility) {
            return $this->checkUserApiToken();
        }

        return true;
    }

    /**
     * @param ApiClientInterface $user
     * @param string             $providerKey
     *
     * @return GuardTokenInterface
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey): GuardTokenInterface
    {
        /** @var string[] $roles */
        $roles = $user->getRoles();
        return new PostAuthenticationGuardToken($user, $providerKey, $roles);
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['message' => self::UNAUTHORIZED_MESSAGE], Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        // all requests need to authenticate, continue processing of the request
        return null;
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
