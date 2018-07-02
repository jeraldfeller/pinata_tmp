<?php

namespace Vivo\SiteBundle\Templating;

use Symfony\Bridge\Twig\AppVariable as BaseAppVariable;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Seo\Page as Seo;

class AppVariable extends BaseAppVariable
{
    /**
     * @var SiteFactory
     */
    private $siteFactory;

    /**
     * @var Seo
     */
    private $seo;

    /**
     * @param SiteFactory $site
     *
     * @return $this
     */
    public function setSiteFactory(SiteFactory $siteFactory)
    {
        $this->siteFactory = $siteFactory;

        return $this;
    }

    /**
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    public function getSite()
    {
        if (null !== $site = $this->siteFactory->get()) {
            return $site;
        }

        throw new \RuntimeException('The "app.site" variable is not available.');
    }

    /**
     * @param Seo $seo
     *
     * @return $this
     */
    public function setSeo(Seo $seo)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * @return Seo
     */
    public function getSeo()
    {
        if (null === $this->seo) {
            throw new \RuntimeException('The "app.seo" variable is not available.');
        }

        return $this->seo;
    }
}
