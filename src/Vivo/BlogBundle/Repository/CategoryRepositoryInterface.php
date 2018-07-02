<?php

namespace Vivo\BlogBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * CategoryRepositoryInterface.
 */
interface CategoryRepositoryInterface extends ObjectRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createAdminListQueryBuilder();

    /**
     * @return \Vivo\BlogBundle\Model\CategoryInterface[]
     */
    public function findAll();

    /**
     * @param $alias
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllWithPostsQueryBuilder($alias);

    /**
     * @return \Vivo\BlogBundle\Model\CategoryInterface[]
     */
    public function findAllWithPosts();

    /**
     * @param $slug
     *
     * @return \Vivo\BlogBundle\Model\CategoryInterface|null
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
     * Count posts for categories.
     *
     * @param \Vivo\BlogBundle\Model\CategoryInterface[] $categories
     */
    public function countPostsForCategories(array $categories);

    /**
     * Creates a new instance of CategoryInterface.
     *
     * @return \Vivo\BlogBundle\Model\CategoryInterface
     */
    public function createCategory();
}
