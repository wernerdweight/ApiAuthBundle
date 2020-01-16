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

    /**
     * @param string $token
     *
     * @return ApiUserTokenInterface
     */
    public function setToken(string $token): ApiUserTokenInterface
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param \DateTime $expirationDate
     *
     * @return ApiUserTokenInterface
     */
    public function setExpirationDate(\DateTime $expirationDate): ApiUserTokenInterface
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

    /**
     * @param ApiUserInterface $apiUser
     *
     * @return ApiUserTokenInterface
     */
    public function setApiUser(ApiUserInterface $apiUser): ApiUserTokenInterface
    {
        $this->apiUser = $apiUser;
        return $this;
    }

    /**
     * @return ApiUserInterface
     */
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
