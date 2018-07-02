<?php

namespace App\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FruitRepository extends EntityRepository
{
    /**
     * @return \App\CoreBundle\Entity\Fruit[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('fruit')
            ->orderBy('fruit.rank', 'ASC')
            ->addOrderBy('fruit.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \App\CoreBundle\Entity\Fruit[]
     */
    public function findAllForListPage()
    {
        return $this->createQueryBuilder('fruit')
            ->orderBy('fruit.rank', 'ASC')
            ->addOrderBy('fruit.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get active fruits.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActiveQueryBuilder()
    {
        return $this->createQueryBuilder('f')
            ->addSelect('s')
            ->innerJoin('f.primarySlug', 's')
            ->andWhere('f.disabledAt IS NULL')
            ->orderBy('f.rank', 'ASC')
            ->addOrderBy('f.id', 'ASC');
    }

    /**
     * @param $id
     *
     * @return \App\CoreBundle\Entity\Fruit
     */
    public function findOneById($id)
    {
        return $this->createQueryBuilder('fruit')
            ->andWhere('fruit.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @return \App\CoreBundle\Entity\Fruit[]
     */
    public function findCurrentFruits()
    {
        return $this->getCurrentFruitsQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCurrentFruitsQueryBuilder()
    {
        return $this->createQueryBuilder('fruit')
            ->orderBy('fruit.rank', 'ASC')
            ;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSearchIndexQueryBuilder()
    {
        return $this->createQueryBuilder('fruit')
            ->where('fruit.private = :private')
            ->andWhere('fruit.disabledAt is null')
            ->setParameter('private', false)
            ->orderBy('fruit.date', 'DESC')
            ;
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
        return $this->createQueryBuilder('fruit')
            ->leftJoin('fruit.slugs', 'slug')
            ->leftJoin('fruit.slugs', 'slug_find')
            ->where('slug_find.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findDefaultFruit()
    {
        return $this->getCurrentFruitsQueryBuilder()->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
