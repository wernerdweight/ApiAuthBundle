<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use ReflectionClass;
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
     * @param string $controllerPath
     *
     * @return bool
     */
    public function isTargeted(string $controllerPath): bool
    {
        /** @var class-string $className */
        $className = explode('::', $controllerPath)[0];
        $controller = new ReflectionClass($className);
        $configuration = $this->getConfiguration();

        if ($controller->implementsInterface(ApiAuthControllerInterface::class)) {
            return true;
        }

        if ($configuration->length() > 0) {
            if (true === $configuration->contains(self::ANY_CONTROLLER)) {
                return true;
            }

            $configuration->rewind();
            while (true === $configuration->valid()) {
                $targetedClass = $configuration->current();
                if ($controller->getName() === $targetedClass ||
                    $controller->implementsInterface($targetedClass) ||
                    $controller->isSubclassOf($targetedClass)
                ) {
                    return true;
                }
                $configuration->next();
            }
        }

        return false;
    }
}
