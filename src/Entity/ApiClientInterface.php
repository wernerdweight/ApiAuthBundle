<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface ApiClientInterface extends UserInterface
{
    public function getClientId(): string;

    public function getClientSecret(): string;

    public function getClientScope(): AccessScope;
}
