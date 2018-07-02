<?php

namespace Vivo\SiteBundle\Twig;

use Vivo\SiteBundle\Util\MobileDetect;

class MobileDetectExtension extends \Twig_Extension
{
    /**
     * @var \Vivo\SiteBundle\Util\MobileDetect
     */
    protected $mobileDetect;

    /**
     * @param MobileDetect $mobileDetect
     */
    public function __construct(MobileDetect $mobileDetect)
    {
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_mobile', array($this, 'isMobile')),
            new \Twig_SimpleFunction('is_tablet', array($this, 'isTablet')),
        );
    }

    /**
     * @return bool
     */
    public function isMobile()
    {
        return $this->mobileDetect->isMobile();
    }

    /**
     * @return bool
     */
    public function isTablet()
    {
        return $this->mobileDetect->isTablet();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_site_mobile_detect';
    }
}
