<?php

namespace Vivo\TreeBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Vivo\TreeBundle\Model\TreeInterface;
use Vivo\TreeBundle\Model\TreeLevel;

trait TreeRepositoryTrait
{
    /**
     * Remove entity and move the direct children up.
     *
     * @param TreeInterface $entity
     */
    public function removeFromTree(TreeInterface $entity)
    {
        $directChildren = $this->getFlatArrayChildren($entity, 1);

        foreach ($directChildren as $child) {
            $child->getModel()->setParent($entity->getParent());
        }

        $this->getEntityManager()->remove($entity);
    }

    /**
     * @param TreeInterface $entity
     *
     * @return \Vivo\TreeBundle\Model\TreeInterface[]
     */
    public function findParentsOf(TreeInterface $entity)
    {
        $parents = array();

        while ($p = $this->findParentOf($entity)) {
            $parents[] = $p;
            $entity = $p;
        }

        krsort($parents);

        return array_values($parents);
    }

    /**
     * @param TreeInterface $entity
     *
     * @return \Vivo\TreeBundle\Model\TreeInterface|null
     */
    public function findParentOf(TreeInterface $entity)
    {
        if (!$entity->getParent()) {
            return;
        }

        return $this->getParentOfQueryBuilder($entity)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getOneOrNullResult();
    }

    /**
     * @param TreeInterface $entity
     *
     * @return QueryBuilder
     */
    public function getParentOfQueryBuilder(TreeInterface $entity)
    {
        return $this->createQueryBuilder('e')
            ->where('e = :parent')
            ->setParameter('parent', $entity->getParent())
            ->setMaxResults(1);
    }

    /**
     * @param TreeInterface $parent
     * @param null          $maxLevels
     * @param QueryBuilder  $qb
     * @param int           $level
     *
     * @return \Vivo\TreeBundle\Model\TreeLevel[]
     */
    public function getFlatArrayChildren(TreeInterface $parent = null, $maxLevels = null, QueryBuilder $qb = null, $level = 0)
    {
        $qb = null === $qb ? $qb : clone $qb;
        $results = array();
        $children = $this->findChildren($parent, 0, $qb);

        foreach ($children as $child) {
            $results[] = new TreeLevel($child, $level);
            $nextLevel = $level + 1;
            $getChildren = null === $maxLevels || $nextLevel < $maxLevels ? true : false;

            if ($getChildren) {
                foreach ($this->getFlatArrayChildren($child, $maxLevels, $qb, $nextLevel) as $result) {
                    $results[] = $result;
                }
            }
        }

        return $results;
    }

    /**
     * @param TreeInterface $parent
     * @param null          $maxLevels
     * @param QueryBuilder  $qb
     * @param int           $level
     *
     * @return TreeInterface[]
     */
    public function findChildren(TreeInterface $parent = null, $maxLevels = null, QueryBuilder $qb = null, $level = 0)
    {
        $qb = null === $qb ? $qb : clone $qb;
        $nextLevel = $level + 1;
        $getChildren = null === $maxLevels || $nextLevel < $maxLevels ? true : false;

        $results = $this->getChildrenQueryBuilder($parent, $getChildren, $qb)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, $this->getChildrenCacheTtl())
            ->getResult();

        if ($getChildren) {
            foreach ($results as $result) {
                $this->findChildren($result, $maxLevels, $qb, $nextLevel);
            }
        }

        return $results;
    }

    /**
     * @param TreeInterface $parent
     * @param bool          $getChildren
     * @param QueryBuilder  $qb
     *
     * @return QueryBuilder
     */
    public function getChildrenQueryBuilder(TreeInterface $parent = null, $getChildren = true, QueryBuilder $qb = null)
    {
        $qb = null === $qb ? $qb : clone $qb;

        if (null === $qb) {
            $qb = $this->createQueryBuilder('e')
                ->addOrderBy('e.rank', 'ASC')
                ->addOrderBy('e.id', 'ASC');
        }

        $rootAlias = $qb->getRootAliases()[0];

        if ($getChildren) {
            $qb->addSelect('children')
                ->leftJoin($rootAlias.'.children', 'children');
        }

        if (null === $parent) {
            $qb = $qb->andWhere($rootAlias.'.parent IS NULL');
        } else {
            $qb = $qb->andWhere($rootAlias.'.parent = :parent')
                ->setParameter('parent', $parent);
        }

        return $qb;
    }

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
     * Get child cache ttl.
     *
     * @return int
     */
    protected function getChildrenCacheTtl()
    {
        return 86400;
    }

    /**
     * @return EntityManager
     */
    abstract protected function getEntityManager();
}
