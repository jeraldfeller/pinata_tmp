<?php

namespace App\CoreBundle\Repository;

use App\CoreBundle\Entity\PromoBlock;
use Doctrine\ORM\EntityRepository;

class PromoBlockGroupRepository extends EntityRepository
{
    public function findGroupWithBlock(PromoBlock $block)
    {
        return $this->createQueryBuilder('g')
            ->join('g.blocks', 'b')
            ->where('b = :block')
            ->setParameter('block', $block)
            ->getQuery()
            ->getResult();
    }
}
