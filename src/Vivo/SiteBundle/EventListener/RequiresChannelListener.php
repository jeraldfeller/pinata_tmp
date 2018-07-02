<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Util\HostUtil;

class RequiresChannelListener
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @var int
     */
    protected $httpPort;

    /**
     * @var int
     */
    protected $httpsPort;

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     * @param SiteFactory     $siteFactory
     * @param int             $httpPort
     * @param int             $httpsPort
     */
    public function __construct(RouterInterface $router, SiteFactory $siteFactory, $httpPort, $httpsPort)
    {
        $this->router = $router;
        $this->siteFactory = $siteFactory;
        $this->httpPort = $httpPort;
        $this->httpsPort = $httpsPort;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->isMethodSafe() || $request->isXmlHttpRequest()) {
            return;
        }

        $currentRoute = $request->attributes->get('routeDocument');

        if (!$currentRoute instanceof Route) {
            $currentRoute = $this->router->getRouteCollection()->get($request->attributes->get('_route'));
        }

        if ($currentRoute instanceof Route) {
            if (null === $site = $this->siteFactory->get()) {
                return;
            }

            if (!$domain = $site->getPrimaryDomain()) {
                return;
            }

            if (HostUtil::format($domain->getHost()) !== HostUtil::format($request->getHost())) {
                // Hostname isn't the same
                return;
            }

            $requiredTransport = strtolower($currentRoute->getRequirement('_requires_channel'));
            $url = null;
            $path = $request->getPathInfo();

            /*
             * We want to use the raw query string
             * $request->getQueryString() normalized the query params
             * This means that hashed urls (such as runtimeconfig images in liipimaginebundle)
             * will fail because it will sort the array.
             */
            $qs = $request->server->get('QUERY_STRING');
            if ($qs) {
                $qs = '?'.$qs;
            }

            if ('https' === $requiredTransport) {
                if ($request->isSecure() || !$domain->isSecure()) {
                    // Already on https or the domain doesn't handle ssl
                    return;
                }

                if ($domain->isSecure()) {
                    $url = 'https://'.$domain->getHost(true).($this->httpsPort == 443 ? '' : ':'.$this->httpsPort).$request->getBaseUrl().$path.$qs;
                }
            } elseif ('http' === $requiredTransport) {
                if (!$request->isSecure()) {
                    // Already on http
                    return;
                }

                $url = 'http://'.$domain->getHost(true).($this->httpPort == 80 ? '' : ':'.$this->httpPort).$request->getBaseUrl().$path.$qs;
            }

            if (null !== $url) {
                $event->setResponse(new RedirectResponse($url, Response::HTTP_MOVED_PERMANENTLY));
                $event->stopPropagation();
            }
        }
    }
}
