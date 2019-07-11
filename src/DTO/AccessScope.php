<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\DTO;

use WernerDweight\RA\RA;

class AccessScope
{
    /** @var RA */
    private $scope;

    /**
     * AccessScope constructor.
     * @param array $scope
     */
    public function __construct(array $scope)
    {
        $this->scope = new RA($scope, RA::RECURSIVE);
    }
}
