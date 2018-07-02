<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class XHashCookieListener
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (null !== ($xHash = $response->headers->get('X-Hash', null))) {
            $response->headers->setCookie(new Cookie('X-Hash', sha1($xHash), strtotime('+30 days 23:59:59')));
            $response->headers->remove('X-Hash');
        }
    }
}
