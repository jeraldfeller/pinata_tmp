<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Vivo\SiteBundle\Factory\SiteFactory;

/**
 * This listener checks the user is browsing using the primary host for a site.
 */
class PrimaryHostListener
{
    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @param SiteFactory $siteFactory
     */
    public function __construct(SiteFactory $siteFactory)
    {
        $this->siteFactory = $siteFactory;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->handleResponseEvent($event);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->handleResponseEvent($event);
    }

    /**
     * @param GetResponseEvent $event
     */
    protected function handleResponseEvent(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->isMethod('GET')) {
            return;
        }

        $site = $this->siteFactory->get();
        if (!$site || !$site->isLive()) {
            return;
        }

        if ($primaryDomain = $site->getPrimaryDomain()) {
            if ($primaryDomain && $primaryDomain->getHost(true) !== $request->getHttpHost()) {
                $event->setResponse(new RedirectResponse($primaryDomain->getUrl().$request->getRequestUri(), Response::HTTP_MOVED_PERMANENTLY));
                $event->stopPropagation();
            }
        }
    }
}
