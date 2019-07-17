<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;

class ApiClientCredentialsFactory
{
    /**
     * @param Request $request
     *
     * @return ApiClientCredentials
     */
    public function create(Request $request): ApiClientCredentials
    {
        $headers = $request->headers;
        /** @var string $clientId */
        $clientId = $headers->get(ApiAuthEnum::CLIENT_ID_HEADER);
        /** @var string $clientSecret */
        $clientSecret = $headers->get(ApiAuthEnum::CLIENT_SECRET_HEADER);
        return new ApiClientCredentials($clientId, $clientSecret);
    }
}
