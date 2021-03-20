<?php 

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Service\VisitorService;

class RequestListener
{
    private VisitorService $visitorService;

    public function __construct(VisitorService $visitorService)
    {
        $this->visitorService = $visitorService;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($event->isMasterRequest()) {
            $url = $event->getRequest()->getRequestUri();

            if (strpos($url, 'admin') !== false or strpos($url, 'api') !== false or strpos($url, '_wdt') !== false or strpos($url, '_profiler') !== false) {
                return;
            }

            $this->visitorService->addVisit();
            $this->visitorService->addPageVisit($url);
        }
    }
}