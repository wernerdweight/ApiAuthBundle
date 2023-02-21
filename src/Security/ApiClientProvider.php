<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Safe\Exceptions\StringsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\RA\RA;

final class ApiClientProvider implements UserProviderInterface
{
    /**
     * @var ApiClientLoader
     */
    private $apiClientLoader;

    /**
     * @var ApiClientRefresher
     */
    private $apiClientRefresher;

    public function __construct(
        ApiClientLoader $apiClientLoader,
        ApiClientRefresher $apiClientRefresher
    ) {
        $this->apiClientLoader = $apiClientLoader;
        $this->apiClientRefresher = $apiClientRefresher;
    }

    /**
     * @param string $username
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadUserByUsername($username): ApiClientInterface
    {
        return $this->apiClientLoader->load($username);
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function refreshUser(UserInterface $user): ApiClientInterface
    {
        return $this->apiClientRefresher->refresh($user);
    }

    /**
     * @throws \Safe\Exceptions\SplException
     */
    public function supportsClass(string $class): bool
    {
        $implementedInterfaces = new RA(\Safe\class_implements($class));
        return $implementedInterfaces->contains(ApiClientInterface::class);
    }

    /**
     * @throws StringsException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }
}
