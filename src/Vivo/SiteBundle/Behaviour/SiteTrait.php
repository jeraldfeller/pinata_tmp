<?php

namespace Vivo\SiteBundle\Behaviour;

use Vivo\SiteBundle\Model\SiteInterface;

trait SiteTrait
{
    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * Set site.
     *
     * @param \Vivo\SiteBundle\Model\SiteInterface $site
     *
     * @return $this
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }
}
