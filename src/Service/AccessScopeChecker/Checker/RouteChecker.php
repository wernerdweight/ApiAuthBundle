<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;
use WernerDweight\ApiAuthBundle\Exception\RouteCheckerException;

class RouteChecker implements AccessScopeCheckerInterface
{
    /** @var string */
    private const ROUTE_KEY = '_route';

    /** @var Request */
    private $request;

    /**
     * RouteChecker constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        if (null === $request) {
            throw new RouteCheckerException(RouteCheckerException::EXCEPTION_NO_REQUEST);
        }
        $this->request = $request;
    }

    /**
     * @param AccessScope $scope
     * @return string
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function check(AccessScope $scope): string
    {
        $route = $this->request->attributes->get(self::ROUTE_KEY);
        return $scope->isAccessible($route);
    }
}
