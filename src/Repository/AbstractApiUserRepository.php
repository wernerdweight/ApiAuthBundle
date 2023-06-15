<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

/**
 * @method ApiUserInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiUserInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiUserInterface[]    findAll()
 * @method ApiUserInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractApiUserRepository extends ServiceEntityRepository implements ApiUserRepositoryInterface
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByToken(string $token): ?ApiUserInterface
    {
        /** @var ApiUserInterface $apiUser */
        $apiUser = $this->createQueryBuilder('this')
            ->join('this.apiTokens', 'apiToken')
            ->where('apiToken.token = :token')
            ->andWhere('apiToken.expirationDate >= CURRENT_TIMESTAMP()')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
        return $apiUser;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByLoginProperty(string $loginProperty, string $value): ?ApiUserInterface
    {
        $condition = sprintf('lower(this.%s) = lower(:value)', $loginProperty);
        /** @var ApiUserInterface|null $apiUser */
        $apiUser = $this->createQueryBuilder('this')
            ->where($condition)
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
        return $apiUser;
    }
}
