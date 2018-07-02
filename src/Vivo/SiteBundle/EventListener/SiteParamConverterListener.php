<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Vivo\SiteBundle\Factory\SiteFactory;

class SiteParamConverterListener
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
     * Intercept SiteInterface and inject the current site.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (null === $site = $this->siteFactory->get()) {
            return;
        }

        $controller = $event->getController();
        $request = $event->getRequest();

        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && is_callable($controller, '__invoke')) {
            $r = new \ReflectionMethod($controller, '__invoke');
        } else {
            $r = new \ReflectionFunction($controller);
        }

        foreach ($r->getParameters() as $param) {
            if ($param->getClass() && $param->getClass()->isInstance($site)) {
                $request->attributes->set($param->getName(), $site);
            }
        }
    }
}
