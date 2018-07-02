<?php

namespace Vivo\SiteBundle\Seo\Sitemap;

interface ManagerInterface
{
    /**
     * Add a sitemap.
     *
     * @param SitemapInterface $sitemap
     *
     * @return $this
     */
    public function addSitemap(SitemapInterface $sitemap);

    /**
     * Get all sitemaps.
     *
     * @return SitemapInterface[]
     */
    public function getSitemaps();

    /**
     * Returns false if sitemap does not exist.
     *
     * @return UrlInterface[]|bool
     */
    public function getUrls($sitemapName, $page);
}
