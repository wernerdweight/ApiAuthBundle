<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiUserCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Repository\ApiUserRepositoryInterface;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;

class ApiUserLoader
{
    /** @var string */
    private const EXCEPTION_NOT_FOUND = 'There is no ApiUser for given api token!';
    /** @var string */
    private const EXCEPTION_NO_SUCH_CREDENTIALS = 'There is no ApiUser for given credentials!';

    /** @var EntityManager */
    private $entityManaager;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var ApiUserClassResolver */
    private $apiUserClassResolver;

    /**
     * ApiUserProvider constructor.
     *
     * @param EntityManager                $entityManager
     * @param ConfigurationProvider        $configurationProvider
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ApiUserClassResolver         $apiUserClassResolver
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationProvider $configurationProvider,
        UserPasswordEncoderInterface $passwordEncoder,
        ApiUserClassResolver $apiUserClassResolver
    ) {
        $this->entityManaager = $entityManager;
        $this->configurationProvider = $configurationProvider;
        $this->passwordEncoder = $passwordEncoder;
        $this->apiUserClassResolver = $apiUserClassResolver;
    }

    /**
     * @return ApiUserRepositoryInterface
     */
    private function getRepository(): ApiUserRepositoryInterface
    {
        /** @var ApiUserRepositoryInterface $repository */
        $repository = $this->entityManaager->getRepository($this->apiUserClassResolver->getUserClass());
        return $repository;
    }

    /**
     * @param string $username
     *
     * @return ApiUserInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadByUsername(string $username): ApiUserInterface
    {
        $repository = $this->getRepository();

        /** @var ApiUserInterface|null $apiUser */
        $apiUser = $repository->findOneByToken($username);
        if (null === $apiUser) {
            throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
        }
        return $apiUser;
    }

    /**
     * @param ApiUserCredentials $credentials
     *
     * @return ApiUserInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadByCredentials(ApiUserCredentials $credentials): ApiUserInterface
    {
        $loginProperty = $this->configurationProvider->getUserLoginProperty();
        /** @var (ApiUserInterface & UserInterface)|null $user */
        $user = $this->getRepository()->findOneBy([
            $loginProperty => $credentials->getLogin(),
        ]);

        if (null === $user || true !== $this->passwordEncoder->isPasswordValid($user, $credentials->getPassword())) {
            throw new UnauthorizedHttpException(ApiAuthEnum::REALM, self::EXCEPTION_NO_SUCH_CREDENTIALS);
        }

        return $user;
    }
}
