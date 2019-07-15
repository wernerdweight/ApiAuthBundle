<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface ApiClientInterface
{
    /**
     * @return string
     */
    public function getClientId(): string;

    /**
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * @return AccessScope
     */
    public function getClientScope(): AccessScope;
}
