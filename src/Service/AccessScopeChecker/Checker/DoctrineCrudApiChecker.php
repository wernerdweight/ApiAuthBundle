<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WernerDweight\ApiAuthBundle\DTO\AccessScope;
use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\ApiAuthBundle\Exception\DoctrineCrudApiCheckerException;

final class DoctrineCrudApiChecker implements AccessScopeCheckerInterface
{
    /**
     * @var string
     */
    private const ENTITY_NAME_KEY = 'entityName';

    /**
     * @var string
     */
    private const DOCTRINE_CRUD_API_ROUTE_PREFIX = 'wds_doctrine_crud_api_';

    private ?Request $request = null;

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
        if (false === strpos($route, self::DOCTRINE_CRUD_API_ROUTE_PREFIX)) {
            return ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN;
        }

        $action = \Safe\substr($route, strlen(self::DOCTRINE_CRUD_API_ROUTE_PREFIX));
        $entityName = $request->attributes->get(self::ENTITY_NAME_KEY);
        return $scope->isAccessible(\Safe\sprintf('%s.%s', $entityName, $action));
    }

    private function getRequest(): Request
    {
        if (null === $this->request) {
            $request = $this->requestStack->getCurrentRequest();
            if (null === $request) {
                throw new DoctrineCrudApiCheckerException(DoctrineCrudApiCheckerException::EXCEPTION_NO_REQUEST);
            }
            $this->request = $request;
        }
        return $this->request;
    }
}
