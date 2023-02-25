<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Exception;

use WernerDweight\EnhancedException\Exception\AbstractEnhancedException;

class ApiUserProviderException extends AbstractEnhancedException
{
    /**
     * @var int
     */
    public const EXCEPTION_NO_USER_CLASS = 1;

    /**
     * @var int
     */
    public const EXCEPTION_NO_ID = 2;

    /**
     * @var string[]
     */
    protected static $messages = [
        self::EXCEPTION_NO_USER_CLASS => 'User class not configured! You must configure user class if you want to use on-behalf access mode.',
        self::EXCEPTION_NO_ID => 'You cannot refresh a user from the EntityUserProvider that does not contain an identifier. ' .
            'The user object has to be serialized with its own identifier mapped by Doctrine.',
    ];
}
