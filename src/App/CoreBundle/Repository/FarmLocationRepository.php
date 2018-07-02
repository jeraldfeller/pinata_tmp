<?php

namespace App\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FarmLocationRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.farms', 'farms')
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllForListPage()
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.farms', 'farms')
            ->where('l.disabledAt IS NULL')
            ->getQuery()
            ->getResult();
    }
}
