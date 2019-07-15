<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;

abstract class AbstractApiUser implements ApiUserInterface
{
    /** @var ArrayCollection|PersistentCollection */
    protected $apiTokens;

    /** @var array */
    private $userScope = [];

    /**
     * AbstractApiUser constructor.
     */
    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
    }

    /**
     * @param array $userScope
     * @return User
     */
    public function setUserScope(array $userScope): self
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
        return $this;
    }

    /**
     * @param ApiUserTokenInterface $apiUserToken
     * @return bool
     */
    public function removeApiToken(ApiUserTokenInterface $apiUserToken): bool
    {
        return $this->apiTokens->removeElement($apiUserToken);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'userScope' => $this->getUserScope(),
        ];
    }
}
