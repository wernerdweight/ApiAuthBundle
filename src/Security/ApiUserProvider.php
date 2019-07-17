<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\RA\RA;

final class ApiUserProvider implements UserProviderInterface
{
    /** @var ApiUserLoader */
    private $apiUserLoader;

    /** @var ApiUserRefresher */
    private $apiUserRefresher;

    /**
     * ApiUserProvider constructor.
     *
     * @param ApiUserLoader    $apiUserLoader
     * @param ApiUserRefresher $apiUserRefresher
     */
    public function __construct(
        ApiUserLoader $apiUserLoader,
        ApiUserRefresher $apiUserRefresher
    ) {
        $this->apiUserLoader = $apiUserLoader;
        $this->apiUserRefresher = $apiUserRefresher;
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
        return $this->apiUserLoader->loadByUsername($username);
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
        return $this->apiUserRefresher->refresh($user);
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
