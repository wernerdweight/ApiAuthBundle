<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Exception;

use WernerDweight\EnhancedException\Exception\AbstractEnhancedException;

class RouteCheckerException extends AbstractEnhancedException
{
    /**
     * @var int
     */
    public const EXCEPTION_NO_REQUEST = 1;

    /**
     * @var string[]
     */
    protected static $messages = [
        self::EXCEPTION_NO_REQUEST => 'No request exists!',
    ];
}
