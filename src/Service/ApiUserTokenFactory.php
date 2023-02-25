<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use Safe\DateTime;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserTokenInterface;
use WernerDweight\ApiAuthBundle\Event\ApiUserTokenRefreshEvent;
use WernerDweight\ApiAuthBundle\Exception\ApiUserAuthenticatorException;
use WernerDweight\TokenGenerator\TokenGenerator;

class ApiUserTokenFactory
{
    /**
     * @var string|null
     */
    private $tokenClass;

    /**
     * @var ConfigurationProvider
     */
    private $configurationProvider;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * ApiUserTokenFactory constructor.
     *
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        ConfigurationProvider $configurationProvider,
        TokenGenerator $tokenGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->tokenGenerator = $tokenGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function create(ApiUserInterface $user): ApiUserTokenInterface
    {
        $tokenClass = $this->getTokenClass();
        /** @var ApiUserTokenInterface $token */
        $token = new $tokenClass();
        $token->setToken($this->generateToken($user));
        $token->setExpirationDate(
            new DateTime(
                \Safe\sprintf(
                    '+%d seconds',
                    $this->configurationProvider->getUserApiTokenExpirationInterval()
                )
            )
        );
        return $token;
    }

    private function getTokenClass(): string
    {
        if (null === $this->tokenClass) {
            $tokenClass = $this->configurationProvider->getUserToken();
            if (null === $tokenClass) {
                throw new ApiUserAuthenticatorException(ApiUserAuthenticatorException::EXCEPTION_NO_TOKEN_CLASS);
            }
            $this->tokenClass = $tokenClass;
        }
        return $this->tokenClass;
    }

    private function generateToken(ApiUserInterface $user): string
    {
        /** @var ApiUserTokenRefreshEvent $event */
        $event = $this->eventDispatcher->dispatch(new ApiUserTokenRefreshEvent($user));

        $token = $event->getToken();
        if (null === $token) {
            $token = $this->tokenGenerator->generate();
        }
        return $token;
    }
}
