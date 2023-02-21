<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;
use WernerDweight\ApiAuthBundle\Exception\ApiUserProviderException;
use WernerDweight\ApiAuthBundle\Repository\ApiUserRepositoryInterface;

class ApiUserRefresher
{
    /**
     * @var string
     */
    private const EXCEPTION_NOT_FOUND = 'There is no ApiUser for given api token!';

    /**
     * @var string
     */
    private const EXCEPTION_UNSUPPORTED_USER =
        '%s is not a supported authentication class. Make sure your class implements ApiUserInterface!';

    /**
     * @var EntityManager
     */
    private $entityManaager;

    /**
     * @var ApiUserClassResolver
     */
    private $apiUserClassResolver;

    /**
     * ApiUserProvider constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ApiUserClassResolver $apiUserClassResolver
    ) {
        $this->entityManaager = $entityManager;
        $this->apiUserClassResolver = $apiUserClassResolver;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function refresh(UserInterface $user): ApiUserInterface
    {
        if (! $user instanceof ApiUserInterface) {
            throw new UnsupportedUserException(\Safe\sprintf(self::EXCEPTION_UNSUPPORTED_USER, get_class($user)));
        }

        $repository = $this->getRepository();
        if ($repository instanceof UserProviderInterface) {
            /** @var ApiUserInterface $user */
            $user = $repository->refreshUser($user);
            return $user;
        }

        $metadata = $this->entityManaager->getClassMetadata($this->apiUserClassResolver->getUserClass());
        $id = $metadata->getIdentifierValues($user);
        if (0 === count($id)) {
            throw new ApiUserProviderException(ApiUserProviderException::EXCEPTION_NO_ID);
        }

        /** @var ApiUserInterface|null $apiUser */
        $apiUser = $repository->find($id);
        if (null === $apiUser) {
            throw new UserNotFoundException(self::EXCEPTION_NOT_FOUND);
        }
        return $apiUser;
    }

    private function getRepository(): ApiUserRepositoryInterface
    {
        /** @var ApiUserRepositoryInterface $repository */
        $repository = $this->entityManaager->getRepository($this->apiUserClassResolver->getUserClass());
        return $repository;
    }
}
