<?php

namespace Vivo\SiteBundle\Util;

use Symfony\Component\HttpFoundation\RequestStack;

class MobileDetect extends \Mobile_Detect
{
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        if ($request = $requestStack->getCurrentRequest()) {
            parent::__construct($request->server->all());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isMobile($userAgent = null, $httpHeaders = null)
    {
        return parent::isMobile($userAgent, $httpHeaders) && !$this->isTablet($userAgent, $httpHeaders);
    }

    /**
     * {@inheritdoc}
     */
    public function isTablet($userAgent = null, $httpHeaders = null)
    {
        return parent::isTablet($userAgent, $httpHeaders);
    }
}
