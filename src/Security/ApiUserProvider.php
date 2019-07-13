<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
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
    private $userClass;

    /** @var EntityManager */
    private $entityManaager;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /**
     * ApiUserProvider constructor.
     *
     * @param EntityManager         $entityManager
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationProvider $configurationProvider
    ) {
        $this->entityManaager = $entityManager;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return string
     */
    private function getUserClass(): string
    {
        $userClass = $this->configurationProvider->getUserClass();
        if (null === $userClass) {
            throw new ApiUserProviderException(ApiUserProviderException::EXCEPTION_NO_USER_CLASS);
        }
        $this->userClass = $userClass;
    }

    /**
     * @return ObjectRepository
     */
    private function getRepository(): ObjectRepository
    {
        return $this->entityManaager->getRepository($this->getUserClass());
    }

    /**
     * @param string $username
     *
     * @return ApiUserInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadUserByUsername($username): ApiUserInterface
    {
        $repository = $this->getRepository();
        $property = $this->configurationProvider->getUserProperty();
        if (null !== $property) {
            /** @var ApiUserInterface|null $apiUser */
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
            /** @var ApiUserInterface|null $apiUser */
            $apiUser = $repository->loadUserByUsername($username);
            if (null === $apiUser) {
                throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
            }
            return $apiUser;
        }

        throw new \InvalidArgumentException(\Safe\sprintf(self::EXCEPTION_UNABLE_TO_LOAD, get_class($repository)));
    }

    /**
     * @param UserInterface $user
     *
     * @return ApiUserInterface
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
            /** @var ApiUserInterface $user */
            $user = $repository->refreshUser($user);
            return $user;
        }

        $id = $this->entityManaager
            ->getClassMetadata($this->getUserClass())
            ->getIdentifierValues($user);
        if (true === empty($id)) {
            throw new \InvalidArgumentException(self::EXCEPTION_NO_ID);
        }

        /** @var ApiUserInterface|null $apiUser */
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
