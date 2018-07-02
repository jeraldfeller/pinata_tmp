<?php

namespace Vivo\SiteBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * SiteRepositoryInterface.
 */
interface SiteRepositoryInterface extends ObjectRepository
{
    /**
     * Find primary site.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface|null
     */
    public function findPrimarySite();

    /**
     * Return all sites with Domain collection.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface[]
     */
    public function findAllWithDomains();

    /**
     * @return QueryBuilder
     */
    public function getAllWithDomainsQueryBuilder();

    /**
     * Return site matching id with domains.
     *
     * @param int $siteId
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface|null
     */
    public function findOneByIdWithDomains($siteId);

    /**
     * @param string $hostname
     * @param bool   $cache
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface|null
     */
    public function findOneByHost($host, $cache = true);

    /**
     * Creates a new instance of SiteInterface.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    public function createSite();
}
