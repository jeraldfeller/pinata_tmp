<?php

namespace Vivo\PageBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Vivo\PageBundle\Model\PageInterface;

interface PageRepositoryInterface extends ObjectRepository
{
    /**
     * @param $alias
     *
     * @return \Vivo\PageBundle\Model\PageInterface[]
     */
    public function findNonNavigationalPages();

    /**
     * @param $alias
     *
     * @return \Vivo\PageBundle\Model\PageInterface|null
     */
    public function findOnePageByAlias($alias);

    /**
     * @param $id
     *
     * @return \Vivo\PageBundle\Model\PageInterface|null
     */
    public function findOnePageByIdWithAllJoins($id);

    /**
     * @return \Vivo\PageBundle\Model\PageInterface|null
     */
    public function findFirstPage();

    /**
     * Find a page by page type.
     *
     * @param string $pageTypeAlias
     *
     * @return \Vivo\PageBundle\Model\PageInterface|null
     */
    public function findOnePageByPageTypeAlias($pageTypeAlias);

    /**
     * @return \Vivo\PageBundle\Model\PageInterface[]
     */
    public function findAllWithSlugs();

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPagesWithSlugsQueryBuilder();

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPageWithPrimarySlugQueryBuilder();

    /**
     * @param PageInterface $page
     *
     * @return \Vivo\PageBundle\Model\PageInterface
     */
    public function findOneByPageWithAllJoins(PageInterface $page);

    /**
     * Creates a new instance of PageInterface.
     *
     * @return \Vivo\PageBundle\Model\PageInterface
     */
    public function createPage();
}
