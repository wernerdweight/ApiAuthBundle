<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Exception\RouteCheckerException;

final class RouteChecker implements AccessScopeCheckerInterface
{
    private ?Request $request;

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function check(AccessScope $scope): string
    {
        $request = $this->getRequest();
        /** @var string $route */
        $route = $request->attributes->get(ApiAuthEnum::ROUTE_KEY);
        /** @var string|null $routeOverride */
        $routeOverride = $request->attributes->get(ApiAuthEnum::ROUTE_OVERRIDE_KEY);
        if (null !== $routeOverride) {
            $route = $routeOverride;
        }
        return $scope->isAccessible($route);
    }

    private function getRequest(): Request
    {
        if (null === $this->request) {
            $request = $this->requestStack->getCurrentRequest();
            if (null === $request) {
                throw new RouteCheckerException(RouteCheckerException::EXCEPTION_NO_REQUEST);
            }
            $this->request = $request;
        }
        return $this->request;
    }
}
