<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class ApiClientAuthenticatedTokenFactory
{
    public function create(UserInterface $user, string $providerKey): GuardTokenInterface
    {
        /** @var string[] $roles */
        $roles = $user->getRoles();
        return new PostAuthenticationGuardToken($user, $providerKey, $roles);
    }
}
