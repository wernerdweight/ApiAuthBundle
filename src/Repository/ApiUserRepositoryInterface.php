<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Repository;

use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

interface ApiUserRepositoryInterface
{
    /**
     * @param string $token
     * @return ApiUserInterface|null
     */
    public function findOneByToken(string $token): ?ApiUserInterface;
}
