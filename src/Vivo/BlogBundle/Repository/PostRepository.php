<?php

namespace Vivo\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Vivo\BlogBundle\Model\CategoryInterface;

/**
 * PostRepository.
 */
class PostRepository extends EntityRepository implements PostRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAdminListQueryBuilder()
    {
        return $this->createQueryBuilder('post')
            ->addSelect('slug')
            ->leftJoin('post.primarySlug', 'slug')
            ->orderBy('post.updatedAt', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePostsQueryBuilder()
    {
        return $this->createQueryBuilderWithDefaultOrder('post')
            ->addSelect('post_slug')
            ->innerJoin('post.primarySlug', 'post_slug')
            ->where('post.disabledAt is NULL and post.publicationDate <= :now')
            ->setParameter('now', new \DateTime('now'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePostsWithCategoryQueryBuilder(CategoryInterface $category = null)
    {
        $qb = $this->getActivePostsQueryBuilder()
            ->addSelect('categories, category_slug')
            ->leftJoin('post.categories', 'categories')
            ->leftJoin('categories.primarySlug', 'category_slug')
        ;

        if (null !== $category) {
            // We need to join the categories again so category collection has all categories
            $qb->leftJoin('post.categories', 'category')
                ->andWhere('category.id = :category_id')
                ->setParameter('category_id', $category->getId())
            ;
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('post')
            ->addSelect('category, slug')
            ->leftJoin('post.categories', 'category')
            ->leftJoin('post.slugs', 'slug')
            ->leftJoin('post.slugs', 'slug_find')
            ->where('slug_find.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderWithDefaultOrder($alias)
    {
        return $this->createQueryBuilder($alias)
            ->orderBy($alias.'.publicationDate', 'desc')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createPost()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }
}
