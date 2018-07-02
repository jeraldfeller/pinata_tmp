<?php

namespace Vivo\SiteBundle\Seo\Sitemap;

class Manager implements ManagerInterface
{
    /**
     * @var SitemapInterface[]
     */
    protected $sitemaps = [];

    /**
     * {@inheritdoc}
     */
    public function addSitemap(SitemapInterface $sitemap)
    {
        if (!in_array($sitemap, $this->sitemaps)) {
            $this->sitemaps[$this->getUniqueSitemapName($sitemap)] = $sitemap;

            $this->sortSitemaps();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSitemaps()
    {
        return $this->sitemaps;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls($sitemapName, $page)
    {
        if (false !== array_key_exists($sitemapName, $this->sitemaps)) {
            return $this->sitemaps[$sitemapName]->getUrls($page);
        }

        return false;
    }

    /**
     * Returns a unique sitemap name.
     *
     * @param SitemapInterface $sitemap
     *
     * @return string
     */
    protected function getUniqueSitemapName(SitemapInterface $sitemap)
    {
        $name = $sitemap->getName();
        if (false !== array_key_exists($name, $this->sitemaps)) {
            // Sitemap name is already taken
            $name .= '-'.substr(sha1(get_class($sitemap)), 0, 6);
        }

        return str_replace('/', '-', $name);
    }

    /**
     * Sorts the sitemaps by priority.
     */
    protected function sortSitemaps()
    {
        uasort($this->sitemaps, function (SitemapInterface $first, SitemapInterface $second) {
            if ($first === $second) {
                return 0;
            }

            return $first->getPriority() > $second->getPriority() ? -1 : 1;
        });
    }
}
