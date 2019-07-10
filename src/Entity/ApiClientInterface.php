<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface ApiClientInterface extends UserInterface
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
     * @return array
     */
    public function getClientScope(): array;
}
