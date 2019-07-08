<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use WernerDweight\RA\RA;

class ConfigurationProvider
{
    /** @var RA */
    private $targetControllers;

    /**
     * ConfigurationProvider constructor.
     *
     * @param array $targetControllers
     */
    public function __construct(array $targetControllers)
    {
        $this->targetControllers = new RA($targetControllers);
    }

    /**
     * @return RA
     */
    public function getTargetControllers(): RA
    {
        return $this->targetControllers;
    }
}
