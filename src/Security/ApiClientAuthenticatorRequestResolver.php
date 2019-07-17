<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Service\TargetControllerResolver;

class ApiClientAuthenticatorRequestResolver
{
    /** @var string */
    private const CONTROLLER_KEY = '_controller';

    /** @var string|null */
    private $apiUserToken;

    /** @var string */
    private $route;

    /** @var Security */
    private $security;

    /** @var TargetControllerResolver */
    private $targetControllerResolver;

    /**
     * ApiClientAuthenticator constructor.
     *
     * @param Security                 $security
     * @param TargetControllerResolver $targetControllerResolver
     */
    public function __construct(
        Security $security,
        TargetControllerResolver $targetControllerResolver
    ) {
        $this->security = $security;
        $this->targetControllerResolver = $targetControllerResolver;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        if (null !== $this->security->getUser()) {
            // already authenticated (not a stateless api)
            return false;
        }

        // check target controllers
        $controller = $request->attributes->get(self::CONTROLLER_KEY);
        if (true !== $this->targetControllerResolver->isTargeted($controller)) {
            return false;
        }

        $headers = $request->headers;
        if (true !== $headers->has(ApiAuthEnum::CLIENT_ID_HEADER) ||
            true !== $headers->has(ApiAuthEnum::CLIENT_SECRET_HEADER)
        ) {
            return false;
        }

        $this->route = $request->attributes->get(ApiAuthEnum::ROUTE_KEY);

        if ($headers->has(ApiAuthEnum::API_USER_TOKEN_HEADER)) {
            /** @var string $token */
            $token = $headers->get(ApiAuthEnum::API_USER_TOKEN_HEADER);
            $this->apiUserToken = $token;
        }

        return true;
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
