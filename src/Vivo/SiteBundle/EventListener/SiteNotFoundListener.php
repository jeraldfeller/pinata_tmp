<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Vivo\SiteBundle\Factory\SiteFactory;

class SiteNotFoundListener
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @param RouterInterface   $router
     * @param \Twig_Environment $twig
     * @param SiteFactory       $site
     */
    public function __construct(RouterInterface $router, \Twig_Environment $twig, SiteFactory $siteFactory)
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->siteFactory = $siteFactory;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->checkSiteExists($event);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->checkSiteExists($event);
    }

    /**
     * @param GetResponseEvent $event
     */
    protected function checkSiteExists(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (preg_match('/^(_wdt|_profiler)$/i', $event->getRequest()->attributes->get('_route'))) {
            return;
        }

        if (null === $this->siteFactory->get()) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
            $response->setContent($this->twig->render('@VivoSite/Site/site_not_found.html.twig'));

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}
