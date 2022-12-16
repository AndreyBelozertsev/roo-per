<?php
namespace Portal\HelperBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Portal\HelperBundle\Helper\PortalLogger;

/**
 * Class RequestListener
 *
 * Simple listener, that provides request information for a logger
 *
 * @package Portal\HelperBundle\EventListener
 */
class RequestListener
{
    private $logger;

    public function __construct(PortalLogger $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logger->log($event->getRequest(), $event->getResponse());
    }
}