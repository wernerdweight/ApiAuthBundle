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
    /** @var string */
    private const ENTITY_NAME_KEY = 'entityName';
    /** @var string */
    private const DOCTRINE_CRUD_API_ROUTE_PREFIX = 'wds_doctrine_crud_api_';

    /** @var Request */
    private $request;

    /** @var RequestStack */
    private $requestStack;

    /**
     * RouteChecker constructor.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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

    /**
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function check(AccessScope $scope): string
    {
        $attributes = $this->getRequest()->attributes;
        $route = $attributes->get(ApiAuthEnum::ROUTE_KEY);
        if (false === strpos($route, self::DOCTRINE_CRUD_API_ROUTE_PREFIX)) {
            return ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN;
        }

        $action = \Safe\substr($route, strlen(self::DOCTRINE_CRUD_API_ROUTE_PREFIX));
        $entityName = $attributes->get(self::ENTITY_NAME_KEY);
        return $scope->isAccessible(\Safe\sprintf('%s.%s', $entityName, $action));
    }
}
