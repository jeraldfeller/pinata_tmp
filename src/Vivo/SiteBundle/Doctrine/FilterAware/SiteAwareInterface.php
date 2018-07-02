<?php

namespace Vivo\SiteBundle\Doctrine\FilterAware;

use Vivo\SiteBundle\Model\SiteInterface;

interface SiteAwareInterface
{
    /**
     * Set site.
     *
     * @param \Vivo\SiteBundle\Model\SiteInterface $site
     *
     * @return $this
     */
    public function setSite(SiteInterface $site);

    /**
     * Get site.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    public function getSite();
}
