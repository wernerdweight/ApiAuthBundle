<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use WernerDweight\ApiAuthBundle\Service\ApiAuthResolver;
use WernerDweight\ApiAuthBundle\Service\TargetControllerResolver;

final class ApiAuthEventSubscriber implements EventSubscriberInterface
{
    /** @var bool */
    private $shouldBeEnhanced = false;

    /** @var ApiAuthResolver */
    private $resolver;

    /** @var TargetControllerResolver */
    private $targetControllerResolver;

    /**
     * ApiAuthEventSubscriber constructor.
     *
     * @param ApiAuthResolver          $resolver
     * @param TargetControllerResolver $targetControllerResolver
     */
    public function __construct(ApiAuthResolver $resolver, TargetControllerResolver $targetControllerResolver)
    {
        $this->resolver = $resolver;
        $this->targetControllerResolver = $targetControllerResolver;
    }

    /**
     * @param ControllerEvent $event
     *
     * @return ServiceSubscriberInterface
     */
    private function getControllerFromEvent(ControllerEvent $event): ServiceSubscriberInterface
    {
        $controller = $event->getController();
        if (true === is_array($controller)) {
            $controller = $controller[0];
        }
        return $controller;
    }

    /**
     * @param ControllerEvent $event
     */
    public function resolveRequest(ControllerEvent $event): void
    {
        $controller = $this->getControllerFromEvent($event);
        if (true === $this->targetControllerResolver->isTargeted($controller)) {
            $this->shouldBeEnhanced = true;
            $this->resolver->resolve($event->getRequest());
        }
    }

    /**
     * @param ResponseEvent $event
     */
    public function enhanceResponse(ResponseEvent $event): void
    {
        if (true !== $this->shouldBeEnhanced) {
            return;
        }

        $response = $event->getResponse();
        if ($response->getStatusCode() < Response::HTTP_BAD_REQUEST) {
            $response->headers->add(
                $this->resolver->getHeaders($event->getRequest())
            );
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'resolveRequest',
            KernelEvents::RESPONSE => 'enhanceResponse',
        ];
    }
}
