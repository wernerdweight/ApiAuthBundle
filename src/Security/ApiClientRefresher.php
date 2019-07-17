<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\ApiAuthBundle\Exception\ApiClientProviderException;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;

class ApiClientRefresher
{
    /** @var string */
    private const EXCEPTION_NOT_FOUND = 'There is no ApiClient for given client id!';
    /** @var string */
    private const EXCEPTION_UNSUPPORTED_USER =
        '%s is not a supported authentication class. Make sure your class implements ApiClientInterface!';

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
     * @param UserInterface $user
     *
     * @return ApiClientInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function refresh(UserInterface $user): ApiClientInterface
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
            throw new ApiClientProviderException(ApiClientProviderException::EXCEPTION_NO_ID);
        }

        /** @var ApiClientInterface|null $apiClient */
        $apiClient = $repository->find($id);
        if (null === $apiClient) {
            throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
        }
        return $apiClient;
    }
}
