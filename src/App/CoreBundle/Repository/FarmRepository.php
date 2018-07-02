<?php

namespace App\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FarmRepository extends EntityRepository
{
    /**
     * @return \App\CoreBundle\Entity\Farm[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('farm')
            ->orderBy('farm.rank', 'ASC')
            ->addOrderBy('farm.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \App\CoreBundle\Entity\Farm[]
     */
    public function findAllForListPage()
    {
        return $this->createQueryBuilder('farm')
            ->orderBy('farm.rank', 'ASC')
            ->where('farm.disabledAt IS NULL')
            ->addOrderBy('farm.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \App\CoreBundle\Entity\Farm[]
     */
    public function findAllPinataFarms()
    {
        return $this->createQueryBuilder('farm')
            ->where('farm.thirdPartyFarm = 0')
            ->andWhere('farm.disabledAt IS NULL')
            ->orderBy('farm.rank', 'ASC')
            ->addOrderBy('farm.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \App\CoreBundle\Entity\Farm[]
     */
    public function findAllThirdPartyFarms()
    {
        return $this->createQueryBuilder('farm')
            ->where('farm.thirdPartyFarm = 1')
            ->andWhere('farm.disabledAt IS NULL')
            ->orderBy('farm.rank', 'ASC')
            ->addOrderBy('farm.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return \App\CoreBundle\Entity\Farm
     */
    public function findOneById($id)
    {
        return $this->createQueryBuilder('farm')
            ->andWhere('farm.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return \App\CoreBundle\Entity\Farm[]
     */
    public function findCurrentFarms()
    {
        return $this->getCurrentFarmsQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCurrentFarmsQueryBuilder()
    {
        return $this->createQueryBuilder('farm')
            ->orderBy('farm.rank', 'ASC')
        ;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSearchIndexQueryBuilder()
    {
        return $this->createQueryBuilder('farm')
            ->where('farm.private = :private')
            ->andWhere('farm.disabledAt is null')
            ->setParameter('private', false)
            ->orderBy('farm.date', 'DESC')
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
        return $this->createQueryBuilder('farm')
            ->leftJoin('farm.slugs', 'slug')
            ->leftJoin('farm.slugs', 'slug_find')
            ->where('slug_find.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findDefaultFarm()
    {
        return $this->getCurrentFarmsQueryBuilder()->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
    
    /**
     * @param $rank
     *
     * @return \App\CoreBundle\Entity\Farm
     */
    public function findNextBySlugWithMenuNodes($rank)
    {
        return $this->createQueryBuilder('farm')
            ->where('farm.rank = :rank')
            ->andWhere('farm.thirdPartyFarm = 0')
            ->setParameter('rank', $rank+1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    
     /**
     * @param $rank
     *
     * @return \App\CoreBundle\Entity\Farm
     */
    public function findPreBySlugWithMenuNodes($rank)
    {
        return $this->createQueryBuilder('farm')
            ->where('farm.rank = :rank')
            ->andWhere('farm.thirdPartyFarm = 0')
            ->setParameter('rank', $rank-1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
