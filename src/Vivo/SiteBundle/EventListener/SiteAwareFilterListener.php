<?php

namespace Vivo\SiteBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Model\SiteInterface;

/**
 * Class SiteAwareFilterListener.
 *
 * Automatically enables the Doctrine Filter \Vivo\SiteBundle\Doctrine\Filter\SiteFilter
 */
class SiteAwareFilterListener
{
    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @param SiteFactory            $siteFactory
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, SiteFactory $siteFactory)
    {
        $this->siteFactory = $siteFactory;
        $this->em = $em;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->activateFilter();
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->activateFilter();
    }

    /**
     * @return null|SiteInterface
     */
    protected function getSite()
    {
        if (null === $this->site) {
            $this->site = $this->siteFactory->get();
        }

        return $this->site;
    }

    /**
     * @param SiteInterface $site
     * @param bool          $andActivate
     *
     * @return $this
     */
    public function setSite(SiteInterface $site, $andActivate = true)
    {
        $this->site = $site;

        if ($andActivate) {
            $this->activateFilter();
        }

        return $this;
    }

    /**
     * Activate the filter.
     */
    public function activateFilter()
    {
        if (!$site = $this->getSite()) {
            return;
        }

        $this->em->getConfiguration()->addFilter('vivo_site', 'Vivo\SiteBundle\Doctrine\Filter\SiteFilter');

        /** @var \Vivo\SiteBundle\Doctrine\Filter\SiteFilter $filter */
        $filter = $this->em->getFilters()->enable('vivo_site');
        $filter->setParameter('site_id', $site->getId());
    }
}
