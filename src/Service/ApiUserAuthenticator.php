<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

class ApiUserAuthenticator
{
    public function authenticate(): ApiUserInterface
    {
        // TODO: this is called from controller action (if credentials match, only refresh token and return it)
        // TODO: dispatch event on token refresh
        // TODO: dispatch event before user is returned
    }
}
