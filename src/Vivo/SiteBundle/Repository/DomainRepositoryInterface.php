<?php

namespace Vivo\SiteBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * DomainRepositoryInterface.
 */
interface DomainRepositoryInterface extends ObjectRepository
{
    /**
     * Creates a new instance of DomainInterface.
     *
     * @return \Vivo\SiteBundle\Model\DomainInterface
     */
    public function createDomain();
}
