<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface ApiUserInterface extends \JsonSerializable, UserInterface, PasswordAuthenticatedUserInterface
{
    public function addApiToken(ApiUserTokenInterface $apiToken): void;

    public function getCurrentToken(): ?ApiUserTokenInterface;

    public function getUserScope(): AccessScope;
}
