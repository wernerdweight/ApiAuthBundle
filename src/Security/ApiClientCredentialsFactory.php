<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;

class ApiClientCredentialsFactory
{
    public function create(string $clientId, string $clientSecret): ApiClientCredentials
    {
        return new ApiClientCredentials($clientId, $clientSecret);
    }
}
