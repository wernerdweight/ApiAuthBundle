<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiUserCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserTokenInterface;
use WernerDweight\ApiAuthBundle\Event\ApiUserAuthenticatedEvent;
use WernerDweight\ApiAuthBundle\Event\ApiUserTokenRefreshEvent;
use WernerDweight\ApiAuthBundle\Exception\ApiUserAuthenticatorException;
use WernerDweight\ApiAuthBundle\Security\ApiUserProvider;

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

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ApiUserTokenFactory */
    private $apiUserTokenFactory;

    /**
     * ApiUserAuthenticator constructor.
     * @param ApiUserProvider $apiUserProvider
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface $entityManager
     * @param ApiUserTokenFactory $apiUserTokenFactory
     */
    public function __construct(
        ApiUserProvider $apiUserProvider,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        ApiUserTokenFactory $apiUserTokenFactory
    ) {
        $this->apiUserProvider = $apiUserProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->apiUserTokenFactory = $apiUserTokenFactory;
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

        $token = $this->apiUserTokenFactory->create($user);
        $user->addApiToken($token);

        /** @var ApiUserAuthenticatedEvent $event */
        $this->eventDispatcher->dispatch(new ApiUserAuthenticatedEvent($user));

        $this->entityManager->persist($token);
        $this->entityManager->flush();
        return $user;
    }
}
