<?php

namespace Vivo\TreeBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Vivo\TreeBundle\Model\TreeInterface;

interface TreeRepositoryInterface
{
    /**
     * @param TreeInterface $entity
     *
     * @return \Vivo\TreeBundle\Model\TreeInterface[]
     */
    public function findParentsOf(TreeInterface $entity);

    /**
     * @param TreeInterface $entity
     *
     * @return \Vivo\TreeBundle\Model\TreeInterface
     */
    public function findParentOf(TreeInterface $entity);

    /**
     * @param TreeInterface $entity
     *
     * @return QueryBuilder
     */
    public function getParentOfQueryBuilder(TreeInterface $entity);

    /**
     * @param TreeInterface $parent
     * @param null          $maxLevels
     * @param QueryBuilder  $qb
     * @param int           $level
     *
     * @return \Vivo\TreeBundle\Model\TreeLevel[]
     */
    public function getFlatArrayChildren(TreeInterface $parent = null, $maxLevels = null, QueryBuilder $qb = null, $level = 0);

    /**
     * @param TreeInterface $parent
     * @param null          $maxLevels
     * @param QueryBuilder  $qb
     * @param int           $level
     *
     * @return TreeInterface[]
     */
    public function findChildren(TreeInterface $parent = null, $maxLevels = null, QueryBuilder $qb = null, $level = 0);

    /**
     * @param TreeInterface $parent
     * @param bool          $getChildren
     * @param QueryBuilder  $qb
     *
     * @return QueryBuilder
     */
    public function getChildrenQueryBuilder(TreeInterface $parent = null, $getChildren = true, QueryBuilder $qb = null);
}
