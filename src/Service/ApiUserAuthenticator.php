<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiUserCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Event\ApiUserAuthenticatedEvent;
use WernerDweight\ApiAuthBundle\Security\ApiUserLoader;

class ApiUserAuthenticator
{
    /** @var string */
    private const AUTH_HEADER = 'Authorization';
    /** @var string */
    private const EXCEPTION_NO_AUTH = 'No Authorization header is present in request!';

    /** @var ApiUserLoader */
    private $apiUserLoader;

    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ApiUserTokenFactory */
    private $apiUserTokenFactory;

    /**
     * ApiUserAuthenticator constructor.
     *
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        ApiUserLoader $apiUserLoader,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        ApiUserTokenFactory $apiUserTokenFactory
    ) {
        $this->apiUserLoader = $apiUserLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->apiUserTokenFactory = $apiUserTokenFactory;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     * @throws \Safe\Exceptions\UrlException
     */
    public function authenticate(Request $request): ApiUserInterface
    {
        /** @var string|null $auth */
        $auth = $request->headers->get(self::AUTH_HEADER);
        if (null === $auth) {
            throw new BadCredentialsException(self::EXCEPTION_NO_AUTH);
        }
        $credentials = new ApiUserCredentials($auth);
        $user = $this->apiUserLoader->loadByCredentials($credentials);

        $token = $this->apiUserTokenFactory->create($user);
        $user->addApiToken($token);

        $this->eventDispatcher->dispatch(new ApiUserAuthenticatedEvent($user));

        $this->entityManager->persist($token);
        $this->entityManager->flush();
        return $user;
    }
}
