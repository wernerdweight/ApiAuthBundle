<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use WernerDweight\ApiAuthBundle\Event\GetResponseHeadersEvent;

class ApiAuthResolver
{
    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var EventDispatcher */
    private $eventDispatcher;

    /**
     * ApiAuthResolver constructor.
     *
     * @param ConfigurationProvider $configurationProvider
     * @param EventDispatcher       $eventDispatcher
     */
    public function __construct(
        ConfigurationProvider $configurationProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     */
    public function resolve(Request $request): void
    {
        // TODO:
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getHeaders(Request $request): array
    {
        $headers = [];

        // TODO:

        /** @var GetResponseHeadersEvent $event */
        $event = $this->eventDispatcher->dispatch(new GetResponseHeadersEvent($request, $headers));
        return $event->getHeaders();
    }
}
