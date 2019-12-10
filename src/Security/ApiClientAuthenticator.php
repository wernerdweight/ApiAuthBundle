<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class ApiClientAuthenticator implements AuthenticatorInterface
{
    /** @var string */
    private const AUTHORIZATION_REQUIRED_MESSAGE = 'Client id and secret are required to authenticate!';
    /** @var string */
    private const UNAUTHORIZED_MESSAGE =
        'Client id, secret or user api token are invalid, expired or not allowed access!';

    /** @var ApiClientAuthenticatorRequestResolver */
    private $apiClientAuthenticatorRequestResolver;

    /** @var ApiClientCredentialsChecker */
    private $apiClientCredentialsChecker;

    /** @var ApiClientAuthenticatedTokenFactory */
    private $apiClientAuthenticatedTokenFactory;

    /**
     * ApiClientAuthenticator constructor.
     *
     * @param ApiClientAuthenticatorRequestResolver $apiClientAuthenticatorRequestResolver
     * @param ApiClientCredentialsChecker           $apiClientCredentialsChecker
     * @param ApiClientAuthenticatedTokenFactory    $apiClientAuthenticatedTokenFactory
     */
    public function __construct(
        ApiClientAuthenticatorRequestResolver $apiClientAuthenticatorRequestResolver,
        ApiClientCredentialsChecker $apiClientCredentialsChecker,
        ApiClientAuthenticatedTokenFactory $apiClientAuthenticatedTokenFactory
    ) {
        $this->apiClientAuthenticatorRequestResolver = $apiClientAuthenticatorRequestResolver;
        $this->apiClientCredentialsChecker = $apiClientCredentialsChecker;
        $this->apiClientAuthenticatedTokenFactory = $apiClientAuthenticatedTokenFactory;
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
        return $this->apiClientAuthenticatorRequestResolver->supports($request);
    }

    /**
     * @param Request $request
     *
     * @return ApiClientCredentials
     */
    public function getCredentials(Request $request): ApiClientCredentials
    {
        return $this->apiClientAuthenticatorRequestResolver->getCredentials($request);
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
     * @param mixed              $credentials
     * @param ApiClientInterface $user
     *
     * @return bool
     *
     * @throws \Safe\Exceptions\StringsException
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->apiClientCredentialsChecker->check($credentials, $user);
    }

    /**
     * @param UserInterface $user
     * @param string        $providerKey
     *
     * @return GuardTokenInterface
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey): GuardTokenInterface
    {
        return $this->apiClientAuthenticatedTokenFactory->create($user, $providerKey);
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
