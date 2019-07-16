<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

interface ApiUserRepositoryInterface extends ObjectRepository
{
    /**
     * @param string $token
     *
     * @return ApiUserInterface|null
     */
    public function findOneByToken(string $token): ?ApiUserInterface;
}
