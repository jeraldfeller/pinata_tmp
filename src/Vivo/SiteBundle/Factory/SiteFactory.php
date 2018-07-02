<?php

namespace Vivo\SiteBundle\Factory;

use Symfony\Component\HttpFoundation\RequestStack;
use Vivo\SiteBundle\Repository\SiteRepositoryInterface;

/**
 * SiteFactory.
 */
class SiteFactory
{
    /**
     * @var \Vivo\SiteBundle\Repository\SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var bool
     */
    protected $fallbackToPrimarySite;

    /**
     * @var \Vivo\SiteBundle\Model\SiteInterface
     */
    protected $site;

    /**
     * @param SiteRepositoryInterface $siteRepository
     * @param RequestStack            $requestStack
     * @param bool                    $fallbackToPrimarySite
     */
    public function __construct(SiteRepositoryInterface $siteRepository, RequestStack $requestStack, $fallbackToPrimarySite = false)
    {
        $this->siteRepository = $siteRepository;
        $this->requestStack = $requestStack;
        $this->fallbackToPrimarySite = $fallbackToPrimarySite;
    }

    /**
     * @return \Vivo\SiteBundle\Model\SiteInterface|null
     */
    public function get()
    {
        if (null === $this->site) {
            if (null === $request = $this->requestStack->getCurrentRequest()) {
                return;
            }

            $this->site = $this->siteRepository->findOneByHost($request->getHttpHost());

            if (null === $this->site && $this->fallbackToPrimarySite) {
                $this->site = $this->siteRepository->findPrimarySite();
            }
        }

        return $this->site;
    }
}
