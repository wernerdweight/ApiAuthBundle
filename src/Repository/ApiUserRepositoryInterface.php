<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Repository;

use Doctrine\Persistence\ObjectRepository;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

/**
 * @extends ObjectRepository<ApiUserInterface>
 */
interface ApiUserRepositoryInterface extends ObjectRepository
{
    public function findOneByToken(string $token): ?ApiUserInterface;
}
