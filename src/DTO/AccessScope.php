<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\DTO;

use WernerDweight\ApiAuthBundle\Enum\ApiAuthEnum;
use WernerDweight\RA\RA;

final class AccessScope implements \JsonSerializable
{
    /**
     * @var string
     */
    private const PATH_SEPARATOR = '.';

    /**
     * @var RA
     */
    private $scope;

    /**
     * AccessScope constructor.
     *
     * @param mixed[] $scope
     */
    public function __construct(array $scope)
    {
        $this->scope = new RA($scope, RA::RECURSIVE);
    }

    /**
     * @throws \WernerDweight\RA\Exception\RAException
     */
    public function isAccessible(string $key): string
    {
        $currentScope = $this->scope;

        $pathSegments = new RA(explode(self::PATH_SEPARATOR, $key));
        $pathSegments->rewind();
        while (true === $pathSegments->valid()) {
            if (! $currentScope instanceof RA) {
                return ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN;
            }
            /** @var string $key */
            $key = $pathSegments->current();
            if (true !== $currentScope->hasKey($key)) {
                return ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN;
            }
            $currentScope = $currentScope->get($key);
            $pathSegments->next();
        }

        if (ApiAuthEnum::SCOPE_ACCESSIBILITY_ACCESSIBLE === $currentScope || true === $currentScope) {
            return ApiAuthEnum::SCOPE_ACCESSIBILITY_ACCESSIBLE;
        }
        if (ApiAuthEnum::SCOPE_ACCESSIBILITY_ON_BEHALF === $currentScope) {
            return ApiAuthEnum::SCOPE_ACCESSIBILITY_ON_BEHALF;
        }
        return ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return $this->scope->toArray(RA::RECURSIVE);
    }
}
