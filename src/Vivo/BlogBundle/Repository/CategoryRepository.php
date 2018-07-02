<?php

namespace Vivo\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository.
 */
class CategoryRepository extends EntityRepository implements CategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAdminListQueryBuilder()
    {
        return $this->createQueryBuilder('category')
            ->addSelect('slug')
            ->leftJoin('category.primarySlug', 'slug')
            ->orderBy('category.updatedAt', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->createQueryBuilderWithDefaultOrder('category')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllWithPostsQueryBuilder($alias)
    {
        return $this->createQueryBuilderWithDefaultOrder($alias)
            ->addSelect('posts, primary_slug')
            ->leftJoin($alias.'.posts', 'posts')
            ->leftJoin($alias.'.primarySlug', 'primary_slug');
    }

    /**
     * {@inheritdoc}
     */
    public function findAllWithPosts()
    {
        return $this->getAllWithPostsQueryBuilder('category')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('category')
            ->addSelect('slug')
            ->innerJoin('category.primarySlug', 'slug')
            ->innerJoin('category.slugs', 'slug_find')
            ->where('slug_find.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderWithDefaultOrder($alias)
    {
        return $this->createQueryBuilder($alias)
            ->orderBy($alias.'.rank, '.$alias.'.title', 'asc');
    }

    /**
     * {@inheritdoc}
     */
    public function countPostsForCategories(array $categories)
    {
        $results = $this->createQueryBuilder('category')
            ->select('category.id, count(post) as _count')
            ->innerJoin('category.posts', 'post')
            ->andWhere('category in (:categories)')
            ->setParameter('categories', $categories)
            ->groupBy('category.id')
            ->getQuery()
            ->getArrayResult();

        $mapped = array();
        foreach ($results as $result) {
            $mapped[$result['id']] = (int) $result['_count'];
        }

        foreach ($categories as $category) {
            if (!array_key_exists($category->getId(), $mapped)) {
                $mapped[$category->getId()] = 0;
            }
        }

        return $mapped;
    }

    /**
     * {@inheritdoc}
     */
    public function createCategory()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }
}
