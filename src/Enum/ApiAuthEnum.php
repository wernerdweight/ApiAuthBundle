<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Enum;

class ApiAuthEnum
{
    /** @var string */
    public const CLIENT_ID_HEADER = 'X-Client-Id';
    /** @var string */
    public const CLIENT_SECRET_HEADER = 'X-Client-Secret';
    /** @var string */
    public const API_USER_TOKEN_HEADER = 'X-Api-User-Token';

    /** @var string */
    public const SCOPE_ACCESSIBILITY_ACCESSIBLE = 'true';
    /** @var string */
    public const SCOPE_ACCESSIBILITY_FORBIDDEN = 'false';
    /** @var string */
    public const SCOPE_ACCESSIBILITY_ON_BEHALF = 'on-behalf';
    /** @var string[] */
    public const SCOPE_ACCESSIBILITY_OPTIONS = [
        self::SCOPE_ACCESSIBILITY_ACCESSIBLE,
        self::SCOPE_ACCESSIBILITY_FORBIDDEN,
        self::SCOPE_ACCESSIBILITY_ON_BEHALF,
    ];

    /** @var string */
    public const ROUTE_KEY = '_route';
    /** @var string */
    public const REALM = 'Basic realm="API"';
}
