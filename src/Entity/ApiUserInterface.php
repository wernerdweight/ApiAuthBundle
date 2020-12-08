<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface ApiUserInterface extends \JsonSerializable
{
    /**
     * @return ApiUserInterface
     */
    public function addApiToken(ApiUserTokenInterface $apiToken): self;

    public function getCurrentToken(): ?ApiUserTokenInterface;

    public function getUserScope(): AccessScope;
}
