<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Exception;

use WernerDweight\EnhancedException\Exception\AbstractEnhancedException;

class ApiClientProviderException extends AbstractEnhancedException
{
    /** @var int */
    public const EXCEPTION_UNABLE_TO_LOAD = 1;
    /** @var int */
    public const EXCEPTION_NO_ID = 2;

    /** @var string[] */
    protected static $messages = [
        self::EXCEPTION_UNABLE_TO_LOAD => '%s must implement "UserLoaderInterface", or the "property" key must be set for user provider.',
        self::EXCEPTION_NO_ID => 'You cannot refresh a user from the EntityUserProvider that does not contain an identifier. ' .
            'The user object has to be serialized with its own identifier mapped by Doctrine.',
    ];
}
