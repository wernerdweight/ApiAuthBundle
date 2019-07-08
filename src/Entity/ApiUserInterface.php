<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

interface ApiUserInterface extends \JsonSerializable
{
    /**
     * @param string $apiToken
     *
     * @return ApiUserInterface
     */
    public function setApiToken(string $apiToken): self;

    /**
     * @return string
     */
    public function getApiToken(): string;

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
     * @return array
     */
    public function getUserScope(): array;
}
