<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

abstract class AbstractApiUserToken implements ApiUserTokenInterface
{
    /** @var string */
    protected $token;

    /** @var \DateTime */
    protected $expirationDate;

    /** @var ApiUserInterface */
    protected $apiUser;

    public function setToken(string $token): ApiUserTokenInterface
    {
        $this->token = $token;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setExpirationDate(\DateTime $expirationDate): ApiUserTokenInterface
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

    public function setApiUser(ApiUserInterface $apiUser): ApiUserTokenInterface
    {
        $this->apiUser = $apiUser;
        return $this;
    }

    public function getApiUser(): ApiUserInterface
    {
        return $this->apiUser;
    }

    /**
     * @return string[]
     */
    public function jsonSerialize(): array
    {
        return [
            'token' => $this->getToken(),
            'expirationDate' => $this->getExpirationDate()->format('c'),
        ];
    }
}
