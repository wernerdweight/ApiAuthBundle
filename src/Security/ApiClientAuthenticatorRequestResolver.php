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
    /**
     * @var string
     */
    private const CONTROLLER_KEY = '_controller';

    /**
     * @var string
     */
    private const NO_AUTH = '';

    /**
     * @var string|null
     */
    private $apiUserToken;

    /**
     * @var string
     */
    private $route;

    /**
     * @var TargetControllerResolver
     */
    private $targetControllerResolver;

    /**
     * @var ConfigurationProvider
     */
    private $configurationProvider;

    /**
     * @var ApiClientCredentialsFactory
     */
    private $apiClientCredentialsFactory;

    public function __construct(
        TargetControllerResolver $targetControllerResolver,
        ConfigurationProvider $configurationProvider,
        ApiClientCredentialsFactory $apiClientCredentialsFactory
    ) {
        $this->targetControllerResolver = $targetControllerResolver;
        $this->configurationProvider = $configurationProvider;
        $this->apiClientCredentialsFactory = $apiClientCredentialsFactory;
    }

    public function supports(Request $request): bool
    {
        /** @var string $controller */
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

    public function getCredentials(Request $request): ApiClientCredentials
    {
        /** @var string $route */
        $route = $request->attributes->get(ApiAuthEnum::ROUTE_KEY);
        /** @var string|null $routeOverride */
        $routeOverride = $request->attributes->get(ApiAuthEnum::ROUTE_OVERRIDE_KEY);
        if (null !== $routeOverride) {
            $route = $routeOverride;
        }
        $this->route = $route;

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

    public function getApiUserToken(): ?string
    {
        return $this->apiUserToken;
    }

    public function getRoute(): string
    {
        return $this->route;
    }
}
