<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\ApiAuthBundle\Exception\ApiClientProviderException;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;

class ApiClientLoader
{
    /**
     * @var string
     */
    private const EXCEPTION_NOT_FOUND = 'There is no ApiClient for given client id!';

    /**
     * @var EntityManager
     */
    private $entityManaager;

    /**
     * @var ConfigurationProvider
     */
    private $configurationProvider;

    /**
     * ApiUserProvider constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationProvider $configurationProvider
    ) {
        $this->entityManaager = $entityManager;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function load(string $username): ApiClientInterface
    {
        $repository = $this->entityManaager->getRepository($this->configurationProvider->getClientClass());
        $property = $this->configurationProvider->getClientProperty();
        if (null !== $property) {
            /** @var ApiClientInterface|null $apiClient */
            $apiClient = $repository->findOneBy([
                $property => $username,
            ]);
            if (null === $apiClient) {
                throw new UserNotFoundException(self::EXCEPTION_NOT_FOUND);
            }
            return $apiClient;
        }

        if ($repository instanceof UserLoaderInterface) {
            /** @var ApiClientInterface|null $apiClient */
            $apiClient = $repository->loadUserByIdentifier($username);
            if (null === $apiClient) {
                throw new UserNotFoundException(self::EXCEPTION_NOT_FOUND);
            }
            return $apiClient;
        }

        throw new ApiClientProviderException(ApiClientProviderException::EXCEPTION_UNABLE_TO_LOAD, [
            get_class($repository),
        ]);
    }
}
