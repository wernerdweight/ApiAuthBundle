<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use WernerDweight\ApiAuthBundle\Exception\ApiUserProviderException;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;

class ApiUserClassResolver
{
    /** @var string */
    private $userClass;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /**
     * ApiUserProvider constructor.
     *
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return string
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
