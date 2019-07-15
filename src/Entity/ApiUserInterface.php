<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface ApiUserInterface extends \JsonSerializable
{
    /**
     * @param ApiUserTokenInterface $apiToken
     *
     * @return ApiUserInterface
     */
    public function addApiToken(ApiUserTokenInterface $apiToken): self;

    /**
     * @return ApiUserTokenInterface|null
     */
    public function getCurrentToken(): ?ApiUserTokenInterface;

    /**
     * @return AccessScope
     */
    public function getUserScope(): AccessScope;
}
