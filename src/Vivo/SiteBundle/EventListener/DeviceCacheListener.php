<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Vivo\SiteBundle\Util\MobileDetect;

class DeviceCacheListener
{
    /**
     * @var \Mobile_Detect
     */
    protected $mobileDetect;

    /**
     * @var bool
     */
    protected $mobileSupport;

    /**
     * @var bool
     */
    protected $tabletSupport;

    /**
     * @var \Symfony\Component\HttpKernel\HttpCache\Esi
     */
    protected $esi;

    /**
     * @param MobileDetect $mobileDetect
     * @param bool         $mobileSupport
     * @param bool         $tabletSupport
     * @param Esi          $esi
     */
    public function __construct(MobileDetect $mobileDetect, $mobileSupport, $tabletSupport, Esi $esi = null)
    {
        $this->mobileDetect = $mobileDetect;
        $this->mobileSupport = $mobileSupport;
        $this->tabletSupport = $tabletSupport;
        $this->esi = $esi;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null === $this->esi || !$this->esi->hasSurrogateCapability($request)) {
            return;
        }

        $isMobile = $this->mobileDetect->isMobile();
        $isTablet = $this->mobileDetect->isTablet();

        if ($this->mobileSupport && $isMobile) {
            $device = 'mobile';
        } elseif ($this->tabletSupport && ($isTablet || $isMobile)) {
            $device = 'tablet';
        } else {
            $device = 'pc';
        }

        $response = $event->getResponse();
        if ($xHash = $response->headers->get('X-Hash', '')) {
            $xHash .= '-';
        }

        $response->headers->set('X-Hash', $xHash.$device);
    }
}
