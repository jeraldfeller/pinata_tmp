<?php

namespace Vivo\PageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Vivo\PageBundle\Model\PageInterface;

class PageRepository extends EntityRepository implements PageRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findNonNavigationalPages()
    {
        return $this->createQueryBuilder('page')
            ->where('SIZE(page.menuNodes) < 1')
            ->orderBy('page.pageTitle', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOnePageByAlias($alias)
    {
        return $this->createQueryBuilder('page')
            ->where('page.alias = :alias')
            ->setParameter('alias', $alias)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOnePageByIdWithAllJoins($id)
    {
        return $this->getFullQueryBuilder()
            ->andWhere('page.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findFirstPage()
    {
        return $this->getFullQueryBuilder()
            ->orderBy('menu_node.rank', 'ASC')
            ->addOrderBy('menu_node.id', 'ASC')
            ->addOrderBy('page.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageWithPrimarySlugQueryBuilder()
    {
        return $this->createQueryBuilder('page')
            ->addSelect('slug')
            ->leftJoin('page.primarySlug', 'slug');
    }

    /**
     * {@inheritdoc}
     */
    public function findOnePageByPageTypeAlias($pageTypeAlias)
    {
        return $this->createQueryBuilder('page')
            ->where('page.pageType = :page_type')
            ->setParameter('page_type', $pageTypeAlias)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllWithSlugs()
    {
        return $this->getPagesWithSlugsQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getPagesWithSlugsQueryBuilder()
    {
        return $this->createQueryBuilder('page')
            ->addSelect('slug')
            ->leftJoin('page.slugs', 'slug');
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByPageWithAllJoins(PageInterface $page)
    {
        return $this->getFullQueryBuilder()
            ->addSelect('menu')
            ->leftJoin('menu_node.menu', 'menu')
            ->andWhere('page = :page')
            ->setParameter('page', $page)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createPage()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getFullQueryBuilder()
    {
        return $this->createQueryBuilder('page')
            ->select('page, slug, content, menu_node, asset_group, asset, file')
            ->leftJoin('page.slugs', 'slug')
            ->leftJoin('page.content', 'content')
            ->leftJoin('page.menuNodes', 'menu_node')
            ->leftJoin('page.assetGroups', 'asset_group')
            ->leftJoin('asset_group.assets', 'asset')
            ->leftJoin('asset.file', 'file');
    }
}
