<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;
use WernerDweight\RA\RA;

final class ApiClientProvider implements UserProviderInterface
{
    /** @var ApiClientLoader */
    private $apiClientLoader;

    /** @var ApiClientRefresher */
    private $apiClientRefresher;

    /**
     * ApiUserProvider constructor.
     *
     * @param ApiClientLoader    $apiClientLoader
     * @param ApiClientRefresher $apiClientRefresher
     */
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
     * @return ApiClientInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadUserByUsername($username): ApiClientInterface
    {
        return $this->apiClientLoader->load($username);
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
        return $this->apiClientRefresher->refresh($user);
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
