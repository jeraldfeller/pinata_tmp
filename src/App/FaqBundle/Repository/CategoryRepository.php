<?php

namespace App\FaqBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /**
     * @param bool $mustHaveFaq
     *
     * @return \App\FaqBundle\Entity\Faq[]
     */
    public function findCategoriesWithFaqsQueryBuilder($mustHaveFaq = false)
    {
        $qb = $this->createQueryBuilder('category')
            ->addSelect('faq')
            ->orderBy('category.rank', 'ASC')
            ->addOrderBy('category.id', 'ASC')
            ->addOrderBy('faq.rank', 'ASC')
            ->addOrderBy('faq.id', 'ASC');

        if (true === $mustHaveFaq) {
            $qb->innerJoin('category.faqs', 'faq');
        } else {
            $qb->leftJoin('category.faqs', 'faq');
        }

        return $qb->getQuery()
            ->getResult();
    }
}
