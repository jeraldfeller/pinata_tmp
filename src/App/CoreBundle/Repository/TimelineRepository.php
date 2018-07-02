<?php

namespace App\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TimelineRepository extends EntityRepository
{
    /**
     * @return \App\CoreBundle\Entity\Timeline[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('timeline')
            ->addOrderBy('timeline.rank', 'ASC')
            ->addOrderBy('timeline.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \App\CoreBundle\Entity\Timeline[]
     */
    public function findAllForListPage()
    {
        return $this->createQueryBuilder('timeline')
            ->addOrderBy('timeline.rank', 'ASC')
            ->addOrderBy('timeline.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return \App\CoreBundle\Entity\Timeline
     */
    public function findOneById($id)
    {
        return $this->createQueryBuilder('timeline')
            ->andWhere('timeline.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @return \App\CoreBundle\Entity\Timeline[]
     */
    public function findCurrentTimelines()
    {
        return $this->getCurrentTimelinesQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCurrentTimelinesQueryBuilder()
    {
        return $this->createQueryBuilder('timeline')
            ->orderBy('timeline.rank', 'ASC')
            ;
    }
}
