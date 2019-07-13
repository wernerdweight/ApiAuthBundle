<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Exception\ApiUserAuthenticatorException;
use WernerDweight\ApiAuthBundle\Exception\ApiUserProviderException;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;
use WernerDweight\RA\RA;

final class ApiUserProvider implements UserProviderInterface
{
    /** @var string */
    private const EXCEPTION_NOT_FOUND = 'There is no ApiUser for given api token!';
    /** @var string */
    private const EXCEPTION_NO_ID =
        'You cannot refresh a user from the EntityUserProvider that does not contain an identifier. ' .
        'The user object has to be serialized with its own identifier mapped by Doctrine.';
    /** @var string */
    private const EXCEPTION_UNSUPPORTED_USER =
        '%s is not a supported authentication class. Make sure your class implements ApiUserInterface!';
    /** @var string */
    private const EXCEPTION_UNABLE_TO_LOAD =
        '%s must implement "UserLoaderInterface", or the "property" key must be set for user provider.';
    /** @var string */
    private const EXCEPTION_TOKEN_EXPIRED = 'The provided api token is expired! Please obtain a new one.';
    /** @var string */
    private const EXCEPTION_NO_USER_TOKEN =
        'No user token present in headers! You must provide value for the header %s.';

    /** @var EntityManager */
    private $entityManaager;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /**
     * ApiUserProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ConfigurationProvider  $configurationProvider
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationProvider $configurationProvider
    ) {
        $this->entityManaager = $entityManager;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return ObjectRepository
     */
    private function getRepository(): ObjectRepository
    {
        $userClass = $this->configurationProvider->getUserClass();
        if (null === $userClass) {
            throw new ApiUserProviderException(ApiUserProviderException::EXCEPTION_NO_USER_CLASS);
        }
        return $this->entityManaager->getRepository($userClass);
    }

    /**
     * @param string|null $username
     *
     * @return UserInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadUserByUsername($username): ApiUserInterface
    {
        if (null === $username) {
            throw new UnauthorizedHttpException(
                'Basic realm="API"',
                \Safe\sprintf(self::EXCEPTION_NO_USER_TOKEN, ApiAuthEnum::API_USER_TOKEN_HEADER)
            );
        }

        $repository = $this->getRepository();
        $property = $this->configurationProvider->getUserProperty();
        if (null !== $property) {
            /** @var ApiUserInterface $apiUser */
            $apiUser = $repository->findOneBy([$property => $username]);
            if (null === $apiUser) {
                throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
            }
            if ($apiUser->getApiTokenExpirationDate() < new \DateTime()) {
                throw new CredentialsExpiredException(self::EXCEPTION_TOKEN_EXPIRED);
            }
            return $apiUser;
        }

        if ($repository instanceof UserLoaderInterface) {
            return $repository->loadUserByUsername($username);
        }

        throw new \InvalidArgumentException(\Safe\sprintf(self::EXCEPTION_UNABLE_TO_LOAD, get_class($repository)));
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function refreshUser(UserInterface $user): ApiUserInterface
    {
        if (!$user instanceof ApiUserInterface) {
            throw new UnsupportedUserException(\Safe\sprintf(self::EXCEPTION_UNSUPPORTED_USER, get_class($user)));
        }

        $repository = $this->getRepository();
        if ($repository instanceof UserProviderInterface) {
            return $repository->refreshUser($user);
        }

        $id = $this->entityManaager
            ->getClassMetadata($this->configurationProvider->getUserClass())
            ->getIdentifierValues($user);
        if (null === $id) {
            throw new \InvalidArgumentException(self::EXCEPTION_NO_ID);
        }

        $apiUser = $repository->find($id);
        if (null === $apiUser) {
            throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
        }
        return $apiUser;
    }

    /**
     * @param string $class
     *
     * @return bool
     *
     * @throws \Safe\Exceptions\SplException
     */
    public function supportsClass($class): bool
    {
        $implementedInterfaces = new RA(\Safe\class_implements($class));
        return $implementedInterfaces->contains(ApiUserInterface::class);
    }
}
