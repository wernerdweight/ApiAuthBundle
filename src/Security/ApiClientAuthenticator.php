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
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;

final class ApiClientAuthenticator implements AuthenticatorInterface
{
    /** @var string */
    private const UNAUTHORIZED_MESSAGE = 'Client id and secret are required to authenticate!';

    /** @var Security */
    private $security;

    /**
     * ApiClientAuthenticator constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, ?AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(['message' => self::UNAUTHORIZED_MESSAGE], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
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
     * @param ApiClientCredentials $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser(ApiClientCredentials $credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        // TODO: use user provider (in order not to have to expect actual field names)
        // TODO: Implement getUser() method.
    }

    /**
     * @param ApiClientCredentials $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials(ApiClientCredentials $credentials, UserInterface $user): bool
    {
        // validation is implicit (no user will be found if id or secret doesn't match)
        return true;
    }

    /**
     * @param UserInterface $user
     * @param string $providerKey
     * @return GuardTokenInterface
     */
    public function createAuthenticatedToken(UserInterface $user, string $providerKey): GuardTokenInterface
    {
        return new PostAuthenticationGuardToken($user, $providerKey, $user->getRoles());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // TODO: Implement onAuthenticationFailure() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // TODO: Implement onAuthenticationSuccess() method.
    }

    public function supportsRememberMe()
    {
        // TODO: Implement supportsRememberMe() method.
    }
}
