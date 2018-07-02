<?php

namespace Vivo\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Vivo\SiteBundle\Model\DomainInterface;
use Vivo\UtilBundle\Behaviour\Repository\PrimaryRepositoryTrait;

/**
 * DomainRepository.
 */
class DomainRepository extends EntityRepository implements DomainRepositoryInterface
{
    use PrimaryRepositoryTrait;

    /**
     * {@inheritdoc}
     */
    public function createDomain()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }

    /**
     * @param QueryBuilder                           $qb
     * @param \Vivo\SiteBundle\Model\DomainInterface $entity
     */
    protected function addPrimaryScope(QueryBuilder $qb, DomainInterface $entity)
    {
        $qb->andWhere('e.site = :site');

        if ($entity->getSite()->getId()) {
            $qb->setParameter('site', $entity->getSite());
        } else {
            // Site is not set - Do not update anything.
            $qb->setParameter('site', 0);
        }
    }
}
