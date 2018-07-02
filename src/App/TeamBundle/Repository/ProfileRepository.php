<?php

namespace App\TeamBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProfileRepository extends EntityRepository
{
    /**
     * @return \App\TeamBundle\Entity\Profile[]
     */
    public function findAllSortedForAdmin()
    {
        return $this->createQueryBuilder('profile')
            ->orderBy('profile.rank', 'ASC')
            ->addOrderBy('profile.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $id
     *
     * @return \App\TeamBundle\Entity\Profile
     */
    public function findOneForAdminUpdate($id)
    {
        return $this->getAllWithImageQueryBuilder()
            ->resetDQLPart('orderBy')
            ->andWhere('profile = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createAdminListQueryBuilder()
    {
        return $this->createQueryBuilder('profile')
            ->orderBy('profile.updatedAt', 'desc');
    }

    /**
     * @return \App\TeamBundle\Entity\Profile[]
     */
    public function findAllActive()
    {
        return $this->getAllWithImageQueryBuilder()
            ->andWhere('profile.disabledAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllWithImageQueryBuilder()
    {
        return $this->createQueryBuilder('profile')
            ->addSelect('image, file')
            ->leftJoin('profile.image', 'image')
            ->leftJoin('image.file', 'file')
            ->orderBy('profile.rank', 'ASC')
            ->addOrderBy('profile.id', 'ASC');
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('post')
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
    public function findOneBySlugWithMenuNodes($slug)
    {
        return $this->createQueryBuilder('profile')
            ->leftJoin('profile.slugs', 'slug')
            ->leftJoin('profile.slugs', 'slug_find')
            ->where('slug_find.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
