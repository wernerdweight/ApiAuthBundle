<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

abstract class AbstractApiUserToken implements ApiUserTokenInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var \DateTime
     */
    protected $expirationDate;

    /**
     * @var ApiUserInterface
     */
    protected $apiUser;

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setExpirationDate(\DateTime $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

    public function setApiUser(ApiUserInterface $apiUser): void
    {
        $this->apiUser = $apiUser;
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
            'expirationDate' => $this->getExpirationDate()
                ->format('c'),
        ];
    }
}
