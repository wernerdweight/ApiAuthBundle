<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class ApiClientAuthenticator extends AbstractAuthenticator
{
    /**
     * @var string
     */
    private const UNAUTHORIZED_MESSAGE =
        'Client id, secret or user api token are invalid, expired or not allowed access!';

    private ApiClientAuthenticatorRequestResolver $apiClientAuthenticatorRequestResolver;

    private ApiClientCredentialsChecker $apiClientCredentialsChecker;

    private ApiClientAuthenticatedTokenFactory $apiClientAuthenticatedTokenFactory;

    private ApiClientProvider $apiClientProvider;

    public function __construct(
        ApiClientAuthenticatorRequestResolver $apiClientAuthenticatorRequestResolver,
        ApiClientCredentialsChecker $apiClientCredentialsChecker,
        ApiClientAuthenticatedTokenFactory $apiClientAuthenticatedTokenFactory,
        ApiClientProvider $apiClientProvider
    ) {
        $this->apiClientAuthenticatorRequestResolver = $apiClientAuthenticatorRequestResolver;
        $this->apiClientCredentialsChecker = $apiClientCredentialsChecker;
        $this->apiClientAuthenticatedTokenFactory = $apiClientAuthenticatedTokenFactory;
        $this->apiClientProvider = $apiClientProvider;
    }

    public function supports(Request $request): bool
    {
        return $this->apiClientAuthenticatorRequestResolver->supports($request);
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // all requests need to authenticate, continue processing of the request
        return null;
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->apiClientAuthenticatorRequestResolver->getCredentials($request);
        $apiClient = $this->apiClientProvider->loadUserByIdentifier($credentials->getClientId());
        if (true !== $this->apiClientCredentialsChecker->check($credentials, $apiClient)) {
            throw new CustomUserMessageAuthenticationException(self::UNAUTHORIZED_MESSAGE);
        }
        return new SelfValidatingPassport(new UserBadge($apiClient->getClientId()));
    }
}
