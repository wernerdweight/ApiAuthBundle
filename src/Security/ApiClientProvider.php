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

    /** @var string */
    private $apiClientClassName;

    /** @var string|null */
    private $property;

    /** @var EntityManager */
    private $entityManaager;

    /**
     * ApiUserProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $apiClientClassName
     * @param string|null            $property
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        string $apiClientClassName,
        ?string $property = null
    ) {
        $this->entityManaager = $entityManager;
        $this->apiClientClassName = $apiClientClassName;
        $this->property = $property;
    }

    /**
     * @return ObjectRepository
     */
    private function getRepository(): ObjectRepository
    {
        return $this->entityManaager->getRepository($this->apiClientClassName);
    }

    /**
     * @param string $username
     *
     * @return UserInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadUserByUsername($username): ApiClientInterface
    {
        $repository = $this->getRepository();
        if (null !== $this->property) {
            /** @var ApiClientInterface $apiClient */
            $apiClient = $repository->findOneBy([$this->property => $username]);
            if (null === $apiClient) {
                throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
            }
            return $apiClient;
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
    public function refreshUser(UserInterface $user): ApiClientInterface
    {
        if (!$user instanceof ApiClientInterface) {
            throw new UnsupportedUserException(\Safe\sprintf(self::EXCEPTION_UNSUPPORTED_USER, get_class($user)));
        }

        $repository = $this->getRepository();
        if ($repository instanceof UserProviderInterface) {
            return $repository->refreshUser($user);
        }

        $id = $this->entityManaager->getClassMetadata($this->apiClientClassName)->getIdentifierValues($user);
        if (null === $id) {
            throw new \InvalidArgumentException(self::EXCEPTION_NO_ID);
        }

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
