<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use WernerDweight\ApiAuthBundle\Exception\ApiUserProviderException;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;

class ApiUserClassResolver
{
    /** @var class-string */
    private $userClass;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /**
     * ApiUserProvider constructor.
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return class-string
     */
    public function getUserClass(): string
    {
        if (null === $this->userClass) {
            $userClass = $this->configurationProvider->getUserClass();
            if (null === $userClass) {
                throw new ApiUserProviderException(ApiUserProviderException::EXCEPTION_NO_USER_CLASS);
            }
            $this->userClass = $userClass;
        }
        return $this->userClass;
    }
}
