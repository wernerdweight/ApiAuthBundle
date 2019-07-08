<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use Symfony\Contracts\Service\ServiceSubscriberInterface;
use WernerDweight\ApiAuthBundle\Controller\ApiAuthControllerInterface;
use WernerDweight\RA\RA;

class TargetControllerResolver
{
    /** @var string */
    private const ANY_CONTROLLER = '*';

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var RA|null */
    private $configuration;

    /**
     * TargetControllerResolver constructor.
     *
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return RA
     */
    private function getConfiguration(): RA
    {
        if (null === $this->configuration) {
            $this->configuration = $this->configurationProvider->getTargetControllers();
        }
        return $this->configuration;
    }

    /**
     * @param ServiceSubscriberInterface $controller
     *
     * @return bool
     */
    public function isTargeted(ServiceSubscriberInterface $controller): bool
    {
        $configuration = $this->getConfiguration();

        if ($controller instanceof ApiAuthControllerInterface) {
            return true;
        }

        if ($configuration->length() > 0) {
            if (true === $configuration->contains(self::ANY_CONTROLLER)) {
                return true;
            }

            $configuration->rewind();
            while (true === $configuration->valid()) {
                $targetedClass = $configuration->current();
                if ($controller instanceof $targetedClass) {
                    return true;
                }
                $configuration->next();
            }
        }

        return false;
    }
}
