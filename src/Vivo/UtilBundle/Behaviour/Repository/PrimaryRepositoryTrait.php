<?php

namespace Vivo\UtilBundle\Behaviour\Repository;

use Doctrine\ORM\QueryBuilder;

trait PrimaryRepositoryTrait
{
    /**
     * @param mixed|\Vivo\UtilBundle\Behaviour\Model\PrimaryTrait $entity
     */
    public function updatePrimaryEntity($entity)
    {
        if (!$entity->isPrimary()) {
            return;
        }

        $qb = $this->createQueryBuilder('e')
            ->update($this->getEntityName(), 'e')
            ->set('e.primary', '0');

        $this->addPrimaryScope($qb, $entity);

        $qb->andWhere('e.primary = :is_primary')
            ->setParameter('is_primary', true);

        $qb->getQuery()
            ->execute();
    }

    /**
     * @return string
     */
    abstract protected function getEntityName();

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    /**
     * @param QueryBuilder $qb
     * @param $entity
     */
    protected function addPrimaryScope(QueryBuilder $qb, $entity)
    {
    }
}
