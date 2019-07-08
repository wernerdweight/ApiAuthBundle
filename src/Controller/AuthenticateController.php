<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use WernerDweight\ApiAuthBundle\Service\ApiUserAuthenticator;

final class AuthenticateController extends AbstractController implements ApiAuthControllerInterface
{
    /**
     * @param ApiUserAuthenticator $authenticator
     *
     * @return JsonResponse
     */
    public function index(ApiUserAuthenticator $authenticator): JsonResponse
    {
        $user = $authenticator->authenticate();
        return $this->json($user);
    }
}
