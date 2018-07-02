<?php

namespace Vivo\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Vivo\SiteBundle\Util\HostUtil;
use Vivo\UtilBundle\Behaviour\Repository\PrimaryRepositoryTrait;

/**
 * SiteRepository.
 */
class SiteRepository extends EntityRepository implements SiteRepositoryInterface
{
    use PrimaryRepositoryTrait;

    /**
     * @return \Vivo\SiteBundle\Model\SiteInterface[]
     */
    public function findAll()
    {
        return $this->createQueryBuilderWithDefaultOrder('site')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find primary site.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface|null
     */
    public function findPrimarySite()
    {
        return $this->createQueryBuilder('site')
            ->select('site')
            ->orderBy('site.primary', 'DESC')
            ->addOrderBy('site.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllWithDomains()
    {
        return $this->getAllWithDomainsQueryBuilder()
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 3600)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllWithDomainsQueryBuilder()
    {
        return $this->createQueryBuilderWithDefaultOrder('site')
            ->select('site, domain')
            ->leftJoin('site.domains', 'domain');
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdWithDomains($siteId)
    {
        return $this->createQueryBuilder('site')
            ->select('site, domain')
            ->leftJoin('site.domains', 'domain')
            ->where('site.id = :id')
            ->setParameter('id', $siteId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByHost($host, $cache = true)
    {
        $host = HostUtil::format($host);

        $qb = $this->createQueryBuilder('site')
            ->select('site, domains')
            ->innerJoin('site.domains', 'domain_search')
            ->innerJoin('site.domains', 'domains')
            ->where('domain_search.host = :host')
            ->setParameter('host', $host)
            ->getQuery();

        if (true === $cache) {
            $qb->useQueryCache(true)
                ->useResultCache(true, 900);
        }

        return $qb->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createSite()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }

    /**
     * Creates an instance of QueryBuilder with a default order.
     *
     * @param $alias
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderWithDefaultOrder($alias)
    {
        return $this->createQueryBuilder($alias)
            ->orderBy($alias.'.name', 'ASC');
    }
}
