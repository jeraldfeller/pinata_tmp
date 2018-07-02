<?php

namespace Vivo\PageBundle\Seo;

use Knp\Component\Pager\Paginator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;
use Vivo\SiteBundle\Seo\Sitemap\AbstractSitemap;
use Vivo\SiteBundle\Seo\Sitemap\Url;
use Vivo\SiteBundle\Seo\Sitemap\UrlInterface;

class Sitemap extends AbstractSitemap
{
    /**
     * @var \Vivo\PageBundle\Repository\PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var \Knp\Component\Pager\Paginator
     */
    protected $paginator;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @param PageRepositoryInterface $pageRepository
     * @param Paginator               $paginator
     * @param RouterInterface         $router
     */
    public function __construct(PageRepositoryInterface $pageRepository, Paginator $paginator, RouterInterface $router)
    {
        $this->pageRepository = $pageRepository;
        $this->paginator = $paginator;
        $this->router = $router;
    }

    /**
     * @return \Knp\Component\Pager\Pagination\AbstractPagination|\Vivo\PageBundle\Model\PageInterface[]
     */
    public function getPagination($page = 1)
    {
        $queryBuilder = $this->pageRepository->getPagesWithSlugsQueryBuilder()
            ->andWhere('page.disabledAt IS NULL')
            ->andWhere('page.robotsNoIndex != :hideFromSitemapTrue or page.robotsNoFollow != :hideFromSitemapTrue')
            ->andWhere('page.excludedFromSitemap != :hideFromSitemapTrue')
            ->setParameter('hideFromSitemapTrue', true);

        return $this->paginator->paginate($queryBuilder, $page, 100);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls($page)
    {
        $urls = array();

        $pages = $this->getPagination($page);
        foreach ($pages as $page) {
            if (!$page->isDisabled() && null !== $page->getPageTypeInstance()->getRouteName()) {
                $url = $this->router->generate($page->getPageTypeInstance()->getRouteName(), array(), UrlGeneratorInterface::ABSOLUTE_URL);

                $urls[] = new Url($url, $page->getUpdatedAt(), UrlInterface::CHANGE_FREQUENCY_DAILY, 1);
            }
        }

        return $urls;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 200;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pages';
    }
}
