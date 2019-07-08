<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Entity;

interface ApiClientInterface
{
    /**
     * @return string
     */
    public function getClientId(): string;

    /**
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * @return array
     */
    public function getClientScope(): array;
}
