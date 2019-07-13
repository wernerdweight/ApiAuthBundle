<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface ApiUserInterface extends \JsonSerializable
{
    /**
     * @param string $apiToken
     *
     * @return ApiUserInterface
     */
    public function setApiToken(string $apiToken): self;

    /**
     * @return string|null
     */
    public function getApiToken(): ?string;

    /**
     * @param \DateTime $apiTokenExpirationDate
     *
     * @return ApiUserInterface
     */
    public function setApiTokenExpirationDate(\DateTime $apiTokenExpirationDate): self;

    /**
     * @return \DateTime|null
     */
    public function getApiTokenExpirationDate(): ?\DateTime;

    /**
     * @return AccessScope
     */
    public function getUserScope(): AccessScope;
}
