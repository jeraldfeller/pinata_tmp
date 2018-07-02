<?php

namespace Vivo\BlogBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Vivo\BlogBundle\Model\CategoryInterface;
use Vivo\SiteBundle\Model\SiteInterface;

/**
 * PostRepositoryInterface.
 */
interface PostRepositoryInterface extends ObjectRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createAdminListQueryBuilder();

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActivePostsQueryBuilder();

    /**
     * @param CategoryInterface $category
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActivePostsWithCategoryQueryBuilder(CategoryInterface $category = null);

    /**
     * @param SiteInterface $site
     * @param $slug
     *
     * @return \Vivo\BlogBundle\Model\PostInterface|null
     */
    public function findOneBySlug($slug);

    /**
     * Create a new QueryBuilder with default order.
     *
     * @param string $alias
     *
     * @return \Doctrine\ORM\QueryBuilder $qb
     */
    public function createQueryBuilderWithDefaultOrder($alias);

    /**
     * Creates a new instance of PostInterface.
     *
     * @return \Vivo\BlogBundle\Model\PostInterface
     */
    public function createPost();
}
