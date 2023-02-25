<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Safe\Exceptions\StringsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\RA\RA;

final class ApiUserProvider implements UserProviderInterface
{
    /**
     * @var ApiUserLoader
     */
    private $apiUserLoader;

    /**
     * @var ApiUserRefresher
     */
    private $apiUserRefresher;

    public function __construct(
        ApiUserLoader $apiUserLoader,
        ApiUserRefresher $apiUserRefresher
    ) {
        $this->apiUserLoader = $apiUserLoader;
        $this->apiUserRefresher = $apiUserRefresher;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadUserByUsername(string $username): ApiUserInterface
    {
        return $this->apiUserLoader->loadByUsername($username);
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function refreshUser(UserInterface $user): ApiUserInterface
    {
        return $this->apiUserRefresher->refresh($user);
    }

    /**
     * @throws \Safe\Exceptions\SplException
     */
    public function supportsClass(string $class): bool
    {
        $implementedInterfaces = new RA(\Safe\class_implements($class));
        return $implementedInterfaces->contains(ApiUserInterface::class);
    }

    /**
     * @throws StringsException
     */
    public function loadUserByIdentifier(string $identifier): ApiUserInterface
    {
        return $this->loadUserByUsername($identifier);
    }
}
