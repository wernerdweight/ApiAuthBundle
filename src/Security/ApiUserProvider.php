<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\DTO\ApiUserCredentials;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Exception\ApiUserProviderException;
use WernerDweight\ApiAuthBundle\Repository\ApiUserRepositoryInterface;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;
use WernerDweight\RA\RA;

final class ApiUserProvider implements UserProviderInterface
{
    /** @var string */
    private const EXCEPTION_NOT_FOUND = 'There is no ApiUser for given api token!';
    /** @var string */
    private const EXCEPTION_NO_SUCH_CREDENTIALS = 'There is no ApiUser for given credentials!';
    /** @var string */
    private const EXCEPTION_UNSUPPORTED_USER =
        '%s is not a supported authentication class. Make sure your class implements ApiUserInterface!';

    /** @var string */
    private $userClass;

    /** @var EntityManager */
    private $entityManaager;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * ApiUserProvider constructor.
     *
     * @param EntityManager                $entityManager
     * @param ConfigurationProvider        $configurationProvider
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationProvider $configurationProvider,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManaager = $entityManager;
        $this->configurationProvider = $configurationProvider;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return string
     */
    private function getUserClass(): string
    {
        if (null === $this->userClass) {
            $userClass = $this->configurationProvider->getUserClass();
            if (null === $userClass) {
                throw new ApiUserProviderException(ApiUserProviderException::EXCEPTION_NO_USER_CLASS);
            }
            $this->userClass = $userClass;
        }
        return $this->userClass;
    }

    /**
     * @return ApiUserRepositoryInterface
     */
    private function getRepository(): ApiUserRepositoryInterface
    {
        /** @var ApiUserRepositoryInterface $repository */
        $repository = $this->entityManaager->getRepository($this->getUserClass());
        return $repository;
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

        /** @var ApiUserInterface|null $apiUser */
        $apiUser = $repository->findOneByToken($username);
        if (null === $apiUser) {
            throw new UsernameNotFoundException(self::EXCEPTION_NOT_FOUND);
        }
        return $apiUser;
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
            throw new ApiUserProviderException(ApiUserProviderException::EXCEPTION_NO_ID);
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

    /**
     * @param ApiUserCredentials $credentials
     *
     * @return ApiUserInterface
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function loadImplicitUser(ApiUserCredentials $credentials): ApiUserInterface
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
