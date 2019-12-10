<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;

class ApiClientCredentialsFactory
{
    /**
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return ApiClientCredentials
     */
    public function create(string $clientId, string $clientSecret): ApiClientCredentials
    {
        return new ApiClientCredentials($clientId, $clientSecret);
    }
}
