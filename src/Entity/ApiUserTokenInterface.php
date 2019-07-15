<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface ApiUserTokenInterface extends \JsonSerializable
{
    /**
     * @param string $token
     *
     * @return ApiUserTokenInterface
     */
    public function setToken(string $token): self;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @param \DateTime $expirationDate
     *
     * @return ApiUserInterface
     */
    public function setExpirationDate(\DateTime $expirationDate): self;

    /**
     * @return \DateTime
     */
    public function getExpirationDate(): \DateTime;

    /**
     * @param ApiUserInterface $apiUser
     * 
     * @return ApiUserTokenInterface
     */
    public function setApiUser(ApiUserInterface $apiUser): self;

    /**
     * @return ApiUserInterface
     */
    public function getApiUser(): ApiUserInterface;
}
