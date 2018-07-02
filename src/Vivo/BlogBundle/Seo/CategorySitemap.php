<?php

namespace Vivo\BlogBundle\Seo;

use Knp\Component\Pager\Paginator;
use Symfony\Component\Routing\RouterInterface;
use Vivo\BlogBundle\Repository\CategoryRepositoryInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;
use Vivo\SiteBundle\Seo\Sitemap\Url;
use Vivo\SiteBundle\Seo\Sitemap\UrlInterface;

class CategorySitemap extends AbstractSitemap
{
    /**
     * @var \Vivo\BlogBundle\Repository\CategoryRepositoryInterface
     */
    protected $categoryRepository;

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
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PageRepositoryInterface     $pageRepository
     * @param Paginator                   $paginator
     * @param RouterInterface             $router
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository, PageRepositoryInterface $pageRepository, Paginator $paginator, RouterInterface $router)
    {
        $this->categoryRepository = $categoryRepository;
        $this->pageRepository = $pageRepository;
        $this->paginator = $paginator;
        $this->router = $router;
    }

    /**
     * @return \Knp\Component\Pager\Pagination\AbstractPagination|\Vivo\BlogBundle\Model\CategoryInterface[]
     */
    public function getPagination($page = 1)
    {
        if (null !== $this->getBlogPage()) {
            $queryBuilder = $this->categoryRepository->getAllWithPostsQueryBuilder('category')
                ->andWhere('category.robotsNoIndex != :hideFromSitemapTrue or category.robotsNoFollow != :hideFromSitemapTrue')
                ->andWhere('category.excludedFromSitemap != :hideFromSitemapTrue')
                ->setParameter('hideFromSitemapTrue', true);
        } else {
            $queryBuilder = [];
        }

        return $this->paginator->paginate($queryBuilder, $page, 100);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls($page)
    {
        $urls = [];
        if (null === $this->getBlogPage()) {
            return $urls;
        }

        $categories = $this->getPagination($page);
        foreach ($categories as $category) {
            if ($category->getActivePosts()->count() > 0) {
                $url = $this->router->generate('vivo_blog.category.index', array(
                    'slug' => $category->getSlug(),
                ), true);

                $urls[] = new Url($url, $category->getUpdatedAt(), UrlInterface::CHANGE_FREQUENCY_MONTHLY, 0.5);
            }
        }

        return $urls;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'blog-categories';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPageRepository()
    {
        return $this->pageRepository;
    }
}
