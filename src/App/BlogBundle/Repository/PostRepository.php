<?php

namespace App\BlogBundle\Repository;

use Vivo\BlogBundle\Repository\PostRepository as BasePostRepository;

/**
 * PostRepository.
 */
class PostRepository extends BasePostRepository
{
    public function findAllFeatured()
    {
        $qb = $this->getActivePostsQueryBuilder()
            ->andWhere('post.featured = 1')
            ->orderBy('post.publicationDate', 'DESC')
            ->addOrderBy('post.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $qb;
    }
}
