<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;

abstract class AbstractApiUser implements ApiUserInterface
{
    /** @var mixed[] */
    protected $userScope = [];

    /** @var ApiUserTokenInterface|null */
    protected $currentToken;

    /** @var ArrayCollection<int, ApiUserTokenInterface>|PersistentCollection<int, ApiUserTokenInterface> */
    protected $apiTokens;

    /**
     * AbstractApiUser constructor.
     */
    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
    }

    /**
     * @param mixed[] $userScope
     *
     * @return ApiUserInterface
     */
    public function setUserScope(array $userScope): ApiUserInterface
    {
        $this->userScope = $userScope;
        return $this;
    }

    /**
     * @return AccessScope
     */
    public function getUserScope(): AccessScope
    {
        return new AccessScope($this->userScope);
    }

    /**
     * @param ApiUserTokenInterface $apiToken
     *
     * @return ApiUserInterface
     */
    public function addApiToken(ApiUserTokenInterface $apiToken): ApiUserInterface
    {
        $this->apiTokens->add($apiToken);
        $apiToken->setApiUser($this);
        $this->currentToken = $apiToken;
        return $this;
    }

    /**
     * @param ApiUserTokenInterface $apiUserToken
     *
     * @return bool
     */
    public function removeApiToken(ApiUserTokenInterface $apiUserToken): bool
    {
        return $this->apiTokens->removeElement($apiUserToken);
    }

    /**
     * @return ArrayCollection<int, ApiUserTokenInterface>|PersistentCollection<int, ApiUserTokenInterface>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    /**
     * @return ApiUserTokenInterface|null
     */
    public function getCurrentToken(): ?ApiUserTokenInterface
    {
        return $this->currentToken;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'token' => $this->getCurrentToken(),
            'userScope' => $this->getUserScope(),
        ];
    }
}
