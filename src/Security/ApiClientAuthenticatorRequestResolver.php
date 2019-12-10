<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use WernerDweight\ApiAuthBundle\DTO\ApiClientCredentials;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Service\ConfigurationProvider;
use WernerDweight\ApiAuthBundle\Service\TargetControllerResolver;

class ApiClientAuthenticatorRequestResolver
{
    /** @var string */
    private const CONTROLLER_KEY = '_controller';
    /** @var string */
    private const NO_AUTH = '';

    /** @var string|null */
    private $apiUserToken;

    /** @var string */
    private $route;

    /** @var TargetControllerResolver */
    private $targetControllerResolver;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var ApiClientCredentialsFactory */
    private $apiClientCredentialsFactory;

    /**
     * ApiClientAuthenticatorRequestResolver constructor.
     *
     * @param TargetControllerResolver    $targetControllerResolver
     * @param ConfigurationProvider       $configurationProvider
     * @param ApiClientCredentialsFactory $apiClientCredentialsFactory
     */
    public function __construct(
        TargetControllerResolver $targetControllerResolver,
        ConfigurationProvider $configurationProvider,
        ApiClientCredentialsFactory $apiClientCredentialsFactory
    ) {
        $this->targetControllerResolver = $targetControllerResolver;
        $this->configurationProvider = $configurationProvider;
        $this->apiClientCredentialsFactory = $apiClientCredentialsFactory;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        // check target controllers
        $controller = $request->attributes->get(self::CONTROLLER_KEY);
        if (true !== $this->targetControllerResolver->isTargeted($controller)) {
            return false;
        }

        if (true === $this->configurationProvider->getExcludeOptionsRequests() &&
            Request::METHOD_OPTIONS === $request->getMethod()) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     *
     * @return ApiClientCredentials
     */
    public function getCredentials(Request $request): ApiClientCredentials
    {
        $this->route = $request->attributes->get(ApiAuthEnum::ROUTE_KEY);

        $headers = $request->headers;

        /** @var string $clientId */
        $clientId = $headers->get(ApiAuthEnum::CLIENT_ID_HEADER, self::NO_AUTH);
        /** @var string $clientSecret */
        $clientSecret = $headers->get(ApiAuthEnum::CLIENT_SECRET_HEADER, self::NO_AUTH);

        if ($headers->has(ApiAuthEnum::API_USER_TOKEN_HEADER)) {
            /** @var string $token */
            $token = $headers->get(ApiAuthEnum::API_USER_TOKEN_HEADER);
            $this->apiUserToken = $token;
        }

        return $this->apiClientCredentialsFactory->create($clientId, $clientSecret);
    }

    /**
     * @return string|null
     */
    public function getApiUserToken(): ?string
    {
        return $this->apiUserToken;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }
}
