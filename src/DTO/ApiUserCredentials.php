<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\DTO;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ApiUserCredentials
{
    /** @var string */
    private const BASIC_PREFIX = 'Basic ';
    /** @var string */
    private const BASIC_DELIMITER = ':';
    /** @var string */
    private const EXCEPTION_INVALID_AUTH_TYPE = 'Only Basic Authorization is supported!';
    /** @var string */
    private const EXCEPTION_INVALID_AUTH_CREDENTIALS = 'Login and password are expected as credentials!';

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /**
     * ApiUserCredentials constructor.
     *
     * @param string $baseAuthorizationHeader
     *
     * @throws \Safe\Exceptions\UrlException
     * @throws \Safe\Exceptions\StringsException
     */
    public function __construct(string $baseAuthorizationHeader)
    {
        if (0 !== strpos($baseAuthorizationHeader, self::BASIC_PREFIX)) {
            throw new BadCredentialsException(self::EXCEPTION_INVALID_AUTH_TYPE);
        }
        $baseAuthorizationHeader = \Safe\substr($baseAuthorizationHeader, strlen(self::BASIC_PREFIX));
        $baseAuthorizationHeader = \Safe\base64_decode($baseAuthorizationHeader, true);
        $credentials = explode(self::BASIC_DELIMITER, $baseAuthorizationHeader);

        if (2 !== count($credentials)) {
            throw new BadCredentialsException(self::EXCEPTION_INVALID_AUTH_CREDENTIALS);
        }
        $this->login = $credentials[0];
        $this->password = $credentials[1];
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
