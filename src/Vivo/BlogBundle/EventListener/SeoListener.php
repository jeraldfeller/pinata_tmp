<?php

namespace Vivo\BlogBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Vivo\BlogBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Event\Events;
use Vivo\SiteBundle\Seo\PageInterface as SeoPageInterface;

class SeoListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Vivo\SiteBundle\Seo\PageInterface
     */
    private $seoPage;

    public function __construct(EventDispatcherInterface $eventDispatcherInterface, SeoPageInterface $seoPage)
    {
        $this->eventDispatcher = $eventDispatcherInterface;
        $this->seoPage = $seoPage;
    }

    public function prepareSeo(PrepareSeoEvent $eventArgs)
    {
        $this->eventDispatcher->dispatch(Events::PREPARE_SEO, new \Vivo\PageBundle\Event\PrepareSeoEvent($eventArgs->getPage(), $eventArgs->getRequest()));

        if ($eventArgs->getCategory()) {
            $this->seoPage->addBreadcrumb($eventArgs->getCategory()->getTitle(), 'vivo_blog.category.index', array(
                'slug' => $eventArgs->getCategory()->getSlug(),
            ));
        } elseif ($eventArgs->getArchiveDate()) {
            $this->seoPage->addBreadcrumb($eventArgs->getArchiveDate()->format('F Y'), 'vivo_blog.archive.index', array(
                'year' => $eventArgs->getArchiveDate()->format('Y'),
                'month' => $eventArgs->getArchiveDate()->format('m'),
            ));
        }

        if ($eventArgs->getPost()) {
            $this->seoPage->addBreadcrumb($eventArgs->getPost()->getTitle(), 'vivo_blog.post.view', array(
                'year' => $eventArgs->getPost()->getPublicationDate()->format('Y'),
                'month' => $eventArgs->getPost()->getPublicationDate()->format('m'),
                'day' => $eventArgs->getPost()->getPublicationDate()->format('d'),
                'slug' => $eventArgs->getPost()->getSlug(),
            ));

            if ($eventArgs->getPost()->getMetaTitle()) {
                $this->seoPage->setTitleParts(array($eventArgs->getPost()->getMetaTitle()));
            } else {
                if ($eventArgs->getCategory()) {
                    $this->seoPage->addTitlePart($eventArgs->getCategory()->getTitle());
                }

                $this->seoPage->addTitlePart($eventArgs->getPost()->getTitle());
            }

            $this->seoPage->setMetaDescription($eventArgs->getPost()->getMetaDescription());

            if ($firstCategory = $eventArgs->getPost()->getCategories()->first()) {
                $this->seoPage->setCanonicalRouteName('vivo_blog.category.view')
                    ->setCanonicalRouteParameters(array(
                        'category_slug' => $firstCategory->getSlug(),
                        'post_slug' => $eventArgs->getPost()->getSlug(),
                    ))
                ;
            } else {
                $this->seoPage->setCanonicalRouteName('vivo_blog.post.view')
                    ->setCanonicalRouteParameters(array(
                        'year' => $eventArgs->getPost()->getPublicationDate()->format('Y'),
                        'month' => $eventArgs->getPost()->getPublicationDate()->format('m'),
                        'day' => $eventArgs->getPost()->getPublicationDate()->format('d'),
                        'slug' => $eventArgs->getPost()->getSlug(),
                    ))
                ;
            }

            if ($eventArgs->getPost()->hasRobotsNoIndex() && $eventArgs->getPost()->hasRobotsNoFollow()) {
                $this->seoPage->addMeta('name', 'robots', 'noindex,nofollow');
            } elseif ($eventArgs->getPost()->hasRobotsNoIndex()) {
                $this->seoPage->addMeta('name', 'robots', 'noindex');
            } elseif ($eventArgs->getPost()->hasRobotsNoFollow()) {
                $this->seoPage->addMeta('name', 'robots', 'nofollow');
            } else {
                $this->seoPage->addMeta('name', 'robots', null);
            }

            $this->seoPage->addMeta('property', 'og:title', $this->coalesce($eventArgs->getPost()->getSocialTitle(), $eventArgs->getPost()->getMetaTitle(), $eventArgs->getPost()->getTitle()));
            $this->seoPage->addMeta('property', 'og:description', $this->coalesce($eventArgs->getPost()->getSocialDescription(), $eventArgs->getPost()->getMetaDescription()));
        } elseif ($eventArgs->getCategory()) {
            if ($eventArgs->getCategory()->getMetaTitle()) {
                $this->seoPage->setTitleParts(array($eventArgs->getCategory()->getMetaTitle()));
            } else {
                $this->seoPage->addTitlePart($eventArgs->getCategory()->getTitle());
            }

            $this->seoPage->setCanonicalRouteName('vivo_blog.category.index')
                ->setCanonicalRouteParameters(array(
                    'slug' => $eventArgs->getCategory()->getSlug(),
                    //'page' => null !== $eventArgs->getPagination() && $eventArgs->getPagination()->getCurrentPageNumber() > 1 ? $eventArgs->getPagination()->getCurrentPageNumber() : null,
                ))
            ;

            if ($eventArgs->getCategory()->hasRobotsNoIndex() && $eventArgs->getCategory()->hasRobotsNoFollow()) {
                $this->seoPage->addMeta('name', 'robots', 'noindex,nofollow');
            } elseif ($eventArgs->getCategory()->hasRobotsNoIndex()) {
                $this->seoPage->addMeta('name', 'robots', 'noindex');
            } elseif ($eventArgs->getCategory()->hasRobotsNoFollow()) {
                $this->seoPage->addMeta('name', 'robots', 'nofollow');
            } else {
                $this->seoPage->addMeta('name', 'robots', null);
            }

            $this->seoPage->addMeta('property', 'og:title', $this->coalesce($eventArgs->getCategory()->getSocialTitle(), $eventArgs->getCategory()->getMetaTitle(), $eventArgs->getCategory()->getTitle()));
            $this->seoPage->addMeta('property', 'og:description', $this->coalesce($eventArgs->getCategory()->getSocialDescription(), $eventArgs->getCategory()->getMetaDescription()));
        } elseif ($eventArgs->getArchiveDate()) {
            $this->seoPage->addTitlePart($eventArgs->getArchiveDate()->format('F Y'))
                ->setCanonicalRouteName('vivo_blog.archive.index')
                ->setCanonicalRouteParameters(array(
                    'year' => $eventArgs->getArchiveDate()->format('Y'),
                    'month' => $eventArgs->getArchiveDate()->format('m'),
                    //'page' => null !== $eventArgs->getPagination() && $eventArgs->getPagination()->getCurrentPageNumber() > 1 ? $eventArgs->getPagination()->getCurrentPageNumber() : null,
                ));
        }
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
