<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use WernerDweight\RA\RA;

class ConfigurationProvider
{
    /** @var string */
    private $clientClass;

    /** @var string|null */
    private $clientProperty;

    /** @var bool */
    private $clientUseScopeAccessModel;

    /** @var string */
    private $clientAccessScopeChecker;

    /** @var string|null */
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

    /** @var RA */
    private $targetControllers;

    /**
     * ConfigurationProvider constructor.
     *
     * @param string      $clientClass
     * @param string|null $clientProperty
     * @param bool        $clientUseScopeAccessModel
     * @param string      $clientAccessScopeChecker
     * @param string|null $userClass
     * @param string|null $userToken
     * @param string      $userLoginProperty
     * @param int         $userApiTokenExpirationInterval
     * @param bool        $userUseScopeAccessModel
     * @param string      $userAccessScopeChecker
     * @param array       $targetControllers
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
        array $targetControllers
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
    }

    /**
     * @return string
     */
    public function getClientClass(): string
    {
        return $this->clientClass;
    }

    /**
     * @return string|null
     */
    public function getClientProperty(): ?string
    {
        return $this->clientProperty;
    }

    /**
     * @return bool
     */
    public function getClientUseScopeAccessModel(): bool
    {
        return $this->clientUseScopeAccessModel;
    }

    /**
     * @return string
     */
    public function getClientAccessScopeChecker(): string
    {
        return $this->clientAccessScopeChecker;
    }

    /**
     * @return string|null
     */
    public function getUserClass(): ?string
    {
        return $this->userClass;
    }

    /**
     * @return string|null
     */
    public function getUserToken(): ?string
    {
        return $this->userToken;
    }

    /**
     * @return string
     */
    public function getUserLoginProperty(): string
    {
        return $this->userLoginProperty;
    }

    /**
     * @return int
     */
    public function getUserApiTokenExpirationInterval(): int
    {
        return $this->userApiTokenExpirationInterval;
    }

    /**
     * @return bool
     */
    public function getUserUseScopeAccessModel(): bool
    {
        return $this->userUseScopeAccessModel;
    }

    /**
     * @return string
     */
    public function getUserAccessScopeChecker(): string
    {
        return $this->userAccessScopeChecker;
    }

    /**
     * @return RA
     */
    public function getTargetControllers(): RA
    {
        return $this->targetControllers;
    }
}
