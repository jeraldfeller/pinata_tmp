<?php

namespace Vivo\SiteBundle\Seo\Sitemap;

abstract class AbstractSitemap implements SitemapInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    public function getName()
    {
        return sha1(get_class($this));
    }
}
