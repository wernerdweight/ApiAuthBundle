<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Exception;

use WernerDweight\EnhancedException\Exception\AbstractEnhancedException;

class ApiUserAuthenticatorException extends AbstractEnhancedException
{
    /** @var int */
    public const EXCEPTION_NO_TOKEN_CLASS = 1;

    /** @var string[] */
    protected static $messages = [
        self::EXCEPTION_NO_TOKEN_CLASS => 'Token class not configured! You must configure token class if you want to use on-behalf access mode.',
    ];
}
