<?php

namespace Vivo\BlogBundle\Seo;

use Vivo\BlogBundle\PageType\BlogPageType;
use Vivo\SiteBundle\Seo\Sitemap\AbstractSitemap as BaseAbstractSitemap;

abstract class AbstractSitemap extends BaseAbstractSitemap
{
    /**
     * @return null|\Vivo\PageBundle\Model\PageInterface
     */
    protected function getBlogPage()
    {
        $blogPage = $this->getPageRepository()->findOnePageByPageTypeAlias(BlogPageType::ALIAS);

        if (!$blogPage || $blogPage->isDisabled()) {
            return;
        }

        return $blogPage;
    }

    /**
     * @return \Vivo\PageBundle\Repository\PageRepositoryInterface
     */
    abstract protected function getPageRepository();
}
