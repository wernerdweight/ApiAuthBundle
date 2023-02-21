<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Exception;

use WernerDweight\EnhancedException\Exception\AbstractEnhancedException;

class AccessScopeCheckerFactoryException extends AbstractEnhancedException
{
    /**
     * @var int
     */
    public const EXCEPTION_UNKNOWN_CHECKER = 1;

    /**
     * @var string[]
     */
    protected static $messages = [
        self::EXCEPTION_UNKNOWN_CHECKER => 'The requested checker %s was not found! Make sure your checker exists and is correctly tagged.',
    ];
}
