<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;

final class ApiClientAuthenticator implements AuthenticatorInterface
{
    /** @var string */
    private const AUTHORIZATION_REQUIRED_MESSAGE = 'Client id and secret are required to authenticate!';
    /** @var string */
    private const UNAUTHORIZED_MESSAGE = 'Client id and/or secret are invalid!';

    /** @var Security */
    private $security;

    /**
     * ApiClientAuthenticator constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
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
        return new ApiClientCredentials(
            $headers->get(ApiAuthEnum::CLIENT_ID_HEADER),
            $headers->get(ApiAuthEnum::CLIENT_SECRET_HEADER)
        );
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return ApiClientInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        return $userProvider->loadUserByUsername($credentials->getClientId());
    }

    /**
     * @param ApiClientCredentials $credentials
     * @param ApiClientInterface   $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        // TODO: check api client scope
        // TODO: if on-behalf access mode is required:
        //  - authenticate user (inject ApiUserProvider (load by api-user-token))
        //  - check api user scope
        return $user->getClientSecret() === $credentials->getClientSecret();
    }

    /**
     * @param ApiClientInterface $user
     * @param string             $providerKey
     *
     * @return GuardTokenInterface
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey): GuardTokenInterface
    {
        return new PostAuthenticationGuardToken($user, $providerKey, $user->getRoles());
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
