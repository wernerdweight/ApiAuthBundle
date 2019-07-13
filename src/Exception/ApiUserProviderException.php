<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Exception;

use WernerDweight\EnhancedException\Exception\AbstractEnhancedException;

class ApiUserProviderException extends AbstractEnhancedException
{
    /** @var int */
    public const EXCEPTION_NO_USER_CLASS = 1;

    /** @var string[] */
    protected static $messages = [
        self::EXCEPTION_NO_USER_CLASS =>
            'User class not configured! You must configure user class if you want to use on-behalf access mode.',
    ];
}
