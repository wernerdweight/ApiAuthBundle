<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiUserCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Event\ApiUserAuthenticatedEvent;
use WernerDweight\ApiAuthBundle\Event\ApiUserTokenRefreshEvent;
use WernerDweight\ApiAuthBundle\Security\ApiUserProvider;
use WernerDweight\TokenGenerator\TokenGenerator;

class ApiUserAuthenticator
{
    /** @var string */
    private const AUTH_HEADER = 'Authorization';
    /** @var string */
    private const EXCEPTION_NO_AUTH = 'No Authorization header is present in request!';

    /** @var ApiUserProvider */
    private $apiUserProvider;

    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var TokenGenerator */
    private $tokenGenerator;

    /**
     * ApiUserAuthenticator constructor.
     * @param ApiUserProvider $apiUserProvider
     * @param EventDispatcher $eventDispatcher
     * @param ConfigurationProvider $configurationProvider
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
        ApiUserProvider $apiUserProvider,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationProvider $configurationProvider,
        TokenGenerator $tokenGenerator
    ) {
        $this->apiUserProvider = $apiUserProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationProvider = $configurationProvider;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param Request $request
     * @return ApiUserInterface
     * @throws \Safe\Exceptions\StringsException
     * @throws \Safe\Exceptions\UrlException
     */
    public function authenticate(Request $request): ApiUserInterface
    {
        $auth = $request->headers->get(self::AUTH_HEADER);
        if (null === $auth) {
            throw new BadCredentialsException(self::EXCEPTION_NO_AUTH);
        }
        $credentials = new ApiUserCredentials($auth);
        $user = $this->apiUserProvider->loadImplicitUser($credentials);

        /** @var ApiUserTokenRefreshEvent $event */
        $event = $this->eventDispatcher->dispatch(new ApiUserTokenRefreshEvent($user));
        $token = $event->getToken();
        if (null === $token) {
            $token = $this->tokenGenerator->generate();
        }
        $user
            ->setApiToken($token)
            ->setApiTokenExpirationDate(
                new \DateTime(
                    \Safe\sprintf(
                        '+%d seconds',
                        $this->configurationProvider->getUserApiTokenExpirationInterval()
                    )
                )
            );

        /** @var ApiUserAuthenticatedEvent $event */
        $this->eventDispatcher->dispatch(new ApiUserAuthenticatedEvent($user));
        return $user;
    }
}
