<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;
use WernerDweight\RA\RA;

final class ApiClientProvider implements UserProviderInterface
{
    /** @var string */
    private const EXCEPTION_NOT_FOUND = 'There is no ApiClient for given client id!';
    /** @var string */
    private const EXCEPTION_NO_ID =
        'You cannot refresh a user from the EntityUserProvider that does not contain an identifier. ' .
        'The user object has to be serialized with its own identifier mapped by Doctrine.';
    /** @var string */
    private const EXCEPTION_UNSUPPORTED_USER =
        '%s is not a supported authentication class. Make sure your class implements ApiClientInterface!';
    /** @var string */
    private const EXCEPTION_UNABLE_TO_LOAD =
        '%s must implement "UserLoaderInterface", or the "property" key must be set for user provider.';

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
     * @return ObjectRepository
     */
    private function getRepository(): ObjectRepository
    {
        return $this->entityManaager->getRepository($this->configurationProvider->getClientClass());
    }

    /**
     * @param string $username
     *
     * @return ApiClientInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadUserByUsername($username): ApiClientInterface
    {
        $repository = $this->getRepository();
        $property = $this->configurationProvider->getClientProperty();
        if (null !== $property) {
            /** @var ApiClientInterface|null $apiClient */
            $apiClient = $repository->findOneBy([$property => $username]);
            if (null === $apiClient) {
                throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
            }
            return $apiClient;
        }

        if ($repository instanceof UserLoaderInterface) {
            /** @var ApiClientInterface|null $apiClient */
            $apiClient = $repository->loadUserByUsername($username);
            if (null === $apiClient) {
                throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
            }
            return $apiClient;
        }

        throw new \InvalidArgumentException(\Safe\sprintf(self::EXCEPTION_UNABLE_TO_LOAD, get_class($repository)));
    }

    /**
     * @param UserInterface $user
     *
     * @return ApiClientInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function refreshUser(UserInterface $user): ApiClientInterface
    {
        if (!$user instanceof ApiClientInterface) {
            throw new UnsupportedUserException(\Safe\sprintf(self::EXCEPTION_UNSUPPORTED_USER, get_class($user)));
        }

        $repository = $this->getRepository();
        if ($repository instanceof UserProviderInterface) {
            /** @var ApiClientInterface $apiClient */
            $apiClient = $repository->refreshUser($user);
            return $apiClient;
        }

        $id = $this->entityManaager
            ->getClassMetadata($this->configurationProvider->getClientClass())
            ->getIdentifierValues($user);
        if (true === empty($id)) {
            throw new \InvalidArgumentException(self::EXCEPTION_NO_ID);
        }

        /** @var ApiClientInterface|null $apiClient */
        $apiClient = $repository->find($id);
        if (null === $apiClient) {
            throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
        }
        return $apiClient;
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
        return $implementedInterfaces->contains(ApiClientInterface::class);
    }
}
