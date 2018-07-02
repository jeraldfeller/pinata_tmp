<?php

namespace Vivo\PageBundle\EventListener;

use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\PageType\Type\HomepagePageType;
use Vivo\SiteBundle\Seo\PageInterface;

class HomepageSeoListener
{
    /**
     * @var \Vivo\SiteBundle\Seo\PageInterface
     */
    private $seoPage;

    /**
     * @param PageInterface $seoPage
     */
    public function __construct(PageInterface $seoPage)
    {
        $this->seoPage = $seoPage;
    }

    /**
     * @param PrepareSeoEvent $eventArgs
     */
    public function prepareSeo(PrepareSeoEvent $eventArgs)
    {
        if ($eventArgs->getPage()->getPageTypeInstance() instanceof HomepagePageType) {
            $this->seoPage->setTitleParts($eventArgs->getPage()->getMetaTitle() ? array($eventArgs->getPage()->getMetaTitle()) : array());
        }
    }
}
