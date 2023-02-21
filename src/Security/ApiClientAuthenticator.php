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
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
// FIXME: authentication changed a ton between Symfony 5 and 6, this needs to be updated and tested
final class ApiClientAuthenticator extends AbstractAuthenticator //implements AuthenticatorInterface
{
    /**
     * @var string
     */
    private const AUTHORIZATION_REQUIRED_MESSAGE = 'Client id and secret are required to authenticate!';

    /**
     * @var string
     */
    private const UNAUTHORIZED_MESSAGE =
        'Client id, secret or user api token are invalid, expired or not allowed access!';

    /**
     * @var ApiClientAuthenticatorRequestResolver
     */
    private $apiClientAuthenticatorRequestResolver;

    /**
     * @var ApiClientCredentialsChecker
     */
    private $apiClientCredentialsChecker;

    /**
     * @var ApiClientAuthenticatedTokenFactory
     */
    private $apiClientAuthenticatedTokenFactory;

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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function start(Request $request, ?AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([
            'message' => self::AUTHORIZATION_REQUIRED_MESSAGE,
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request): bool
    {
        return $this->apiClientAuthenticatorRequestResolver->supports($request);
    }

    public function getCredentials(Request $request): ApiClientCredentials
    {
        return $this->apiClientAuthenticatorRequestResolver->getCredentials($request);
    }

    /**
     * @param mixed $credentials
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
     * @throws \Safe\Exceptions\StringsException
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->apiClientCredentialsChecker->check($credentials, $user);
    }

    /**
     * @param string $providerKey
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey): GuardTokenInterface
    {
        return $this->apiClientAuthenticatedTokenFactory->create($user, $providerKey);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'message' => self::UNAUTHORIZED_MESSAGE,
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @param string $providerKey
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        // all requests need to authenticate, continue processing of the request
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        // TODO: Implement authenticate() method.
    }
}
