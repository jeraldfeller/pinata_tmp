<?php

namespace Vivo\PageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Vivo\PageBundle\Model\MenuNodeInterface;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\TreeBundle\Model\TreeInterface;
use Vivo\TreeBundle\Repository\TreeRepositoryTrait;
use Vivo\UtilBundle\Behaviour\Repository\PrimaryRepositoryTrait;

class MenuNodeRepository extends EntityRepository implements MenuNodeRepositoryInterface
{
    use PrimaryRepositoryTrait;
    use TreeRepositoryTrait;

    /**
     * {@inheritdoc}
     */
    public function getPrimaryParentTrailOf(PageInterface $page, $allowDisabled = false)
    {
        $lowestRoot = null;
        $lastParents = array();

        foreach ($page->getMenuNodes($allowDisabled) as $node) {
            $parents = $this->findParentsOf($node);
            $parents[] = $node;

            $root = end($parents);

            if ($root->isPrimary()) {
                return $parents;

                break;
            } elseif (null === $lowestRoot || $root->getRank() < $lowestRoot->getRank()) {
                $lowestRoot = $root;
                $lastParents = $parents;
            }
        }

        return $lastParents;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentOfQueryBuilder(TreeInterface $entity)
    {
        return $this->createQueryBuilder('e')
            ->addSelect('page')
            ->leftJoin('e.page', 'page')
            ->where('e = :parent')
            ->setParameter('parent', $entity->getParent())
            ->setMaxResults(1);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneMenu($id)
    {
        return $this->getMenuQueryBuilder()
            ->andWhere('menu_node.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneActiveMenuByAlias($alias)
    {
        return $this->getMenuQueryBuilder()
            ->andWhere('menu_node.alias = :alias')
            ->andWhere('menu_node.disabled = :disabled')
            ->setParameter('alias', $alias)
            ->setParameter('disabled', false)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuQueryBuilder()
    {
        return $this->createQueryBuilder('menu_node')
            ->where('menu_node.parent IS NULL and menu_node.page IS NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveMenuNodesQueryBuilder()
    {
        return $this->createQueryBuilder('menu_node')
            ->where('menu_node.disabled = :disabled')
            ->setParameter('disabled', false)
            ->orderBy('menu_node.rank', 'ASC')
            ->addOrderBy('menu_node.id', 'ASC');
    }

    /**
     * {@inheritdoc}
     */
    public function createMenuNode()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }

    /**
     * @param QueryBuilder                             $qb
     * @param \Vivo\PageBundle\Model\MenuNodeInterface $entity
     */
    protected function addPrimaryScope(QueryBuilder $qb, MenuNodeInterface $entity)
    {
        $qb->andWhere('e.parent IS NULL and e.site = :site')
            ->setParameter('site', $entity->getSite());
    }
}
