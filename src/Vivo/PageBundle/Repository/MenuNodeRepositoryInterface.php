<?php

namespace Vivo\PageBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\TreeBundle\Repository\TreeRepositoryInterface;

interface MenuNodeRepositoryInterface extends TreeRepositoryInterface, ObjectRepository
{
    /**
     * Returns the primary parent trail of a PageInterface.
     *
     * @param PageInterface $page
     * @param bool          $allowDisabled
     *
     * @return array|\Vivo\TreeBundle\Model\TreeInterface[]
     */
    public function getPrimaryParentTrailOf(PageInterface $page, $allowDisabled = false);

    /**
     * @param $id
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface
     */
    public function findOneMenu($id);

    /**
     * @param $alias
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface
     */
    public function findOneActiveMenuByAlias($alias);

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getMenuQueryBuilder();

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActiveMenuNodesQueryBuilder();

    /**
     * Creates a new instance of MenuNodeInterface.
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface
     */
    public function createMenuNode();
}
