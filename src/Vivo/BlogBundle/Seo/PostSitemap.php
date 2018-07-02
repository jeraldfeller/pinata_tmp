<?php

namespace Vivo\BlogBundle\Seo;

use Knp\Component\Pager\Paginator;
use Symfony\Component\Routing\RouterInterface;
use Vivo\BlogBundle\Repository\PostRepositoryInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;
use Vivo\SiteBundle\Seo\Sitemap\Url;
use Vivo\SiteBundle\Seo\Sitemap\UrlInterface;

class PostSitemap extends AbstractSitemap
{
    /**
     * @var \Vivo\BlogBundle\Repository\PostRepositoryInterface
     */
    protected $postRepository;

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
     * @param PostRepositoryInterface $postRepository
     * @param PageRepositoryInterface $pageRepository
     * @param Paginator               $paginator
     * @param RouterInterface         $router
     */
    public function __construct(PostRepositoryInterface $postRepository, PageRepositoryInterface $pageRepository, Paginator $paginator, RouterInterface $router)
    {
        $this->postRepository = $postRepository;
        $this->pageRepository = $pageRepository;
        $this->paginator = $paginator;
        $this->router = $router;
    }

    /**
     * @return \Knp\Component\Pager\Pagination\AbstractPagination|\Vivo\BlogBundle\Model\PostInterface[]
     */
    public function getPagination($page = 1)
    {
        if (null !== $this->getBlogPage()) {
            $queryBuilder = $this->postRepository->getActivePostsQueryBuilder()
                ->andWhere('post.robotsNoIndex != :hideFromSitemapTrue or post.robotsNoFollow != :hideFromSitemapTrue')
                ->andWhere('post.excludedFromSitemap != :hideFromSitemapTrue')
                ->setParameter('hideFromSitemapTrue', true);
        } else {
            $queryBuilder = [];
        }

        return $this->paginator->paginate($queryBuilder, $page, 250);
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

        $posts = $this->getPagination($page);
        foreach ($posts as $post) {
            $url = $this->router->generate('vivo_blog.post.view', array(
                'year' => $post->getPublicationDate()->format('Y'),
                'month' => $post->getPublicationDate()->format('m'),
                'day' => $post->getPublicationDate()->format('d'),
                'slug' => $post->getSlug(),
            ), true);

            $urls[] = new Url($url, $post->getUpdatedAt(), UrlInterface::CHANGE_FREQUENCY_MONTHLY, 0.5);
        }

        return $urls;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'blog-posts';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPageRepository()
    {
        return $this->pageRepository;
    }
}
