<?php

namespace Vivo\PageBundle\EventListener;

use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Repository\MenuNodeRepositoryInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;
use Vivo\PageBundle\Seo\ActivePage;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\SiteBundle\Seo\PageInterface as SeoPageInterface;

class SeoListener
{
    /**
     * @var ActivePage
     */
    private $activePage;

    /**
     * @var \Vivo\SiteBundle\Seo\PageInterface
     */
    private $seoPage;

    /**
     * @var \Vivo\PageBundle\Repository\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Vivo\PageBundle\Repository\MenuNodeRepositoryInterface
     */
    private $menuNodeRepository;

    /**
     * @param ActivePage                  $activePage
     * @param SeoPageInterface            $seoPage
     * @param PageRepositoryInterface     $pageRepository
     * @param MenuNodeRepositoryInterface $menuNodeRepository
     */
    public function __construct(ActivePage $activePage, SeoPageInterface $seoPage, PageRepositoryInterface $pageRepository, MenuNodeRepositoryInterface $menuNodeRepository)
    {
        $this->activePage = $activePage;
        $this->seoPage = $seoPage;
        $this->pageRepository = $pageRepository;
        $this->menuNodeRepository = $menuNodeRepository;
    }

    /**
     * @param PrepareSeoEvent $eventArgs
     */
    public function prepareSeo(PrepareSeoEvent $eventArgs)
    {
        $page = $eventArgs->getPage();

        $this->activePage->setPage($page);
        $this->setTitle($page);
        $this->setBreadcrumbs($page);
        $this->setMeta($page);
        $this->setCanonicalLink($page);
    }

    /**
     * Add title parts.
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    protected function setTitle(PageInterface $page)
    {
        if ($page->getMetaTitle()) {
            $this->seoPage
                ->addTitlePart($page->getMetaTitle());
        } else {
            // Build Title using menu node structure
            $titles = array();

            /** @var \Vivo\PageBundle\Model\MenuNodeInterface[] $primaryParentTrail */
            $primaryParentTrail = $this->menuNodeRepository->getPrimaryParentTrailOf($page, false);
            array_pop($primaryParentTrail);

            foreach ($primaryParentTrail as $menuNode) {
                if ($menuNode->getParent()) {
                    $titles[] = $menuNode->getTitle();
                }
            }

            $titles[] = $page->getPageTitle();

            $this->seoPage->setTitleParts($titles);
        }

        return $this;
    }

    /**
     * Set bread crumbs.
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    protected function setBreadcrumbs(PageInterface $page)
    {
        /** @var \Vivo\PageBundle\Model\MenuNodeInterface[] $primaryParentTrail */
        $primaryParentTrail = $this->menuNodeRepository->getPrimaryParentTrailOf($page, false);

        if (count($primaryParentTrail) > 0) {
            foreach ($primaryParentTrail as $menuNode) {
                if ($menuNode->getParent()) {
                    $this->seoPage->addBreadcrumb($menuNode->getTitle(), $menuNode->getRouteName());
                }
            }
        } else {
            $this->seoPage->addBreadcrumb($page->getPageTitle(), $page->getPageTypeInstance()->getRouteName());
        }

        return $this;
    }

    /**
     * Set canonical Route.
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    protected function setCanonicalLink(PageInterface $page)
    {
        $this->seoPage->setCanonicalRouteName($page->getPageTypeInstance()->getRouteName());

        return $this;
    }

    /**
     * Set meta data.
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    protected function setMeta(PageInterface $page)
    {
        $this->seoPage
            ->setMetaDescription($page->getMetaDescription());

        if ($page->hasRobotsNoIndex() && $page->hasRobotsNoFollow()) {
            $this->seoPage->addMeta('name', 'robots', 'noindex,nofollow');
        } elseif ($page->hasRobotsNoIndex()) {
            $this->seoPage->addMeta('name', 'robots', 'noindex');
        } elseif ($page->hasRobotsNoFollow()) {
            $this->seoPage->addMeta('name', 'robots', 'nofollow');
        } else {
            $this->seoPage->addMeta('name', 'robots', null);
        }

        $this->seoPage->addMeta('property', 'og:title', $this->coalesce($page->getSocialTitle(), $page->getMetaTitle(), $page->getPageTitle()));
        $this->seoPage->addMeta('property', 'og:description', $this->coalesce($page->getSocialDescription(), $page->getMetaDescription()));

        return $this;
    }

    /**
     * @return mixed
     */
    protected function coalesce()
    {
        if (func_num_args() > 0) {
            foreach (func_get_args() as $value) {
                if (null !== $value) {
                    return $value;
                }
            }
        }

        return;
    }
}
