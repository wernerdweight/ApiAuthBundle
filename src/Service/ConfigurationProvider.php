<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use WernerDweight\RA\RA;

class ConfigurationProvider
{
    /** @var class-string */
    private $clientClass;

    /** @var string|null */
    private $clientProperty;

    /** @var bool */
    private $clientUseScopeAccessModel;

    /** @var string */
    private $clientAccessScopeChecker;

    /** @var class-string|null */
    private $userClass;

    /** @var string|null */
    private $userToken;

    /** @var string */
    private $userLoginProperty;

    /** @var int */
    private $userApiTokenExpirationInterval;

    /** @var bool */
    private $userUseScopeAccessModel;

    /** @var string */
    private $userAccessScopeChecker;

    /** @var bool */
    private $excludeOptionsRequests;

    /** @var RA */
    private $targetControllers;

    /**
     * ConfigurationProvider constructor.
     *
     * @param class-string      $clientClass
     * @param class-string|null $userClass
     * @param string[]          $targetControllers
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $clientClass,
        ?string $clientProperty,
        bool $clientUseScopeAccessModel,
        string $clientAccessScopeChecker,
        ?string $userClass,
        ?string $userToken,
        string $userLoginProperty,
        int $userApiTokenExpirationInterval,
        bool $userUseScopeAccessModel,
        string $userAccessScopeChecker,
        array $targetControllers,
        bool $excludeOptionsRequests
    ) {
        $this->clientClass = $clientClass;
        $this->clientProperty = $clientProperty;
        $this->clientUseScopeAccessModel = $clientUseScopeAccessModel;
        $this->clientAccessScopeChecker = $clientAccessScopeChecker;
        $this->userClass = $userClass;
        $this->userToken = $userToken;
        $this->userLoginProperty = $userLoginProperty;
        $this->userApiTokenExpirationInterval = $userApiTokenExpirationInterval;
        $this->userUseScopeAccessModel = $userUseScopeAccessModel;
        $this->userAccessScopeChecker = $userAccessScopeChecker;
        $this->targetControllers = new RA($targetControllers);
        $this->excludeOptionsRequests = $excludeOptionsRequests;
    }

    /**
     * @return class-string
     */
    public function getClientClass(): string
    {
        return $this->clientClass;
    }

    public function getClientProperty(): ?string
    {
        return $this->clientProperty;
    }

    public function getClientUseScopeAccessModel(): bool
    {
        return $this->clientUseScopeAccessModel;
    }

    public function getClientAccessScopeChecker(): string
    {
        return $this->clientAccessScopeChecker;
    }

    /**
     * @return class-string|null
     */
    public function getUserClass(): ?string
    {
        return $this->userClass;
    }

    public function getUserToken(): ?string
    {
        return $this->userToken;
    }

    public function getUserLoginProperty(): string
    {
        return $this->userLoginProperty;
    }

    public function getUserApiTokenExpirationInterval(): int
    {
        return $this->userApiTokenExpirationInterval;
    }

    public function getUserUseScopeAccessModel(): bool
    {
        return $this->userUseScopeAccessModel;
    }

    public function getUserAccessScopeChecker(): string
    {
        return $this->userAccessScopeChecker;
    }

    public function getTargetControllers(): RA
    {
        return $this->targetControllers;
    }

    public function getExcludeOptionsRequests(): bool
    {
        return $this->excludeOptionsRequests;
    }
}
