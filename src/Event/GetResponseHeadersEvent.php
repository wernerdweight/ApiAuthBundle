<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class GetResponseHeadersEvent extends Event
{
    /** @var string */
    public const NAME = 'wds.api_auth_bundle.get_response_headers';

    /** @var array */
    private $headers = [];

    /** @var Request */
    private $request;

    /**
     * GetResponseHeadersEvent constructor.
     *
     * @param Request $request
     * @param array   $headers
     */
    public function __construct(Request $request, array $headers)
    {
        $this->request = $request;
        $this->headers = $headers;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return GetResponseHeadersEvent
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }
}
