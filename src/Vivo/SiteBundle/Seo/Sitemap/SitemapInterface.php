<?php

namespace Vivo\SiteBundle\Seo\Sitemap;

interface SitemapInterface
{
    /**
     * Get pagination.
     *
     * @param int $page
     *
     * @return \Knp\Component\Pager\Pagination\AbstractPagination
     */
    public function getPagination($page = 1);

    /**
     * Get the urls for sitemap.
     *
     * @param int $page
     *
     * @return UrlInterface[]
     */
    public function getUrls($page);

    /**
     * Return the name of this sitemap
     * This string will be used in the route.
     *
     * @return string
     */
    public function getName();

    /**
     * Sitemaps with a higher priority will
     * be displayed first in the index. However,
     * this does not affect ranking etc.
     *
     * @return int
     */
    public function getPriority();
}
