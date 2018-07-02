<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Vivo\SiteBundle\Factory\SiteFactory;

class LiveAccessListener
{
    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @var array
     */
    protected $liveEnvironments;

    /**
     * @var string
     */
    protected $currentEnvironment;

    /**
     * @param SiteFactory $siteFactory
     * @param array       $liveEnvironments
     * @param string      $currentEnvironment
     */
    public function __construct(SiteFactory $siteFactory, array $liveEnvironments, $currentEnvironment)
    {
        $this->siteFactory = $siteFactory;
        $this->liveEnvironments = $liveEnvironments;
        $this->currentEnvironment = $currentEnvironment;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (in_array($this->currentEnvironment, $this->liveEnvironments, true)) {
            return;
        }

        $site = $this->siteFactory->get();
        if (!$site || !$site->isLive()) {
            return;
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $response->setContent('You are not allowed to access this file.');

        $event->setResponse($response);
        $event->stopPropagation();
    }
}
