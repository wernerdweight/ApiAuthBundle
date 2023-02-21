<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

interface ApiUserTokenInterface extends \JsonSerializable
{
    public function setToken(string $token): void;

    public function getToken(): string;

    public function setExpirationDate(\DateTime $expirationDate): void;

    public function getExpirationDate(): \DateTime;

    public function setApiUser(ApiUserInterface $apiUser): void;

    public function getApiUser(): ApiUserInterface;
}
