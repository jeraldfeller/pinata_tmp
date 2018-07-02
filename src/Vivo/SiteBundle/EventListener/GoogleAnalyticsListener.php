<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Vivo\SiteBundle\Factory\SiteFactory;

class GoogleAnalyticsListener
{
    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param SiteFactory       $site
     * @param \Twig_Environment $twig
     */
    public function __construct(SiteFactory $siteFactory, \Twig_Environment $twig)
    {
        $this->siteFactory = $siteFactory;
        $this->twig = $twig;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if ($response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ) {
            return;
        }

        $site = $this->siteFactory->get();

        if (!$site || !$site->isLive() || !$site->getGoogleAnalyticsId()) {
            return;
        }

        $this->injectTrackingCode($response);
    }

    /**
     * Injects the google tracking code into the given response.
     *
     * @param Response $response A Response instance
     */
    protected function injectTrackingCode(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $pos = $posrFunction($content, '</head>');

        if (false !== $pos) {
            $toolbar = $this->twig->render(
                '@VivoSite/Tracking/google.html.twig',
                array(
                    'site' => $this->siteFactory->get(),
                )
            );
            $content = $substrFunction($content, 0, $pos).$toolbar.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }
}
