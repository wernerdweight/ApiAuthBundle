<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;

interface AccessScopeCheckerInterface
{
    /**
     * Returns one of:
     *  - ApiAuthEnum::SCOPE_ACCESSIBILITY_ACCESSIBLE (unrestricted access)
     *  - ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN (access denied)
     *  - ApiAuthEnum::SCOPE_ACCESSIBILITY_ON_BEHALF (access allowed if user provided).
     *
     * @param AccessScope $scope
     *
     * @return string
     */
    public function check(AccessScope $scope): string;
}
