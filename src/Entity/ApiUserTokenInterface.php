<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

interface ApiUserTokenInterface extends \JsonSerializable
{
    /**
     * @return ApiUserTokenInterface
     */
    public function setToken(string $token): self;

    public function getToken(): string;

    /**
     * @return ApiUserTokenInterface
     */
    public function setExpirationDate(\DateTime $expirationDate): self;

    public function getExpirationDate(): \DateTime;

    /**
     * @return ApiUserTokenInterface
     */
    public function setApiUser(ApiUserInterface $apiUser): self;

    public function getApiUser(): ApiUserInterface;
}
