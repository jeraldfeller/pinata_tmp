<?php

namespace Vivo\SiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSiteData extends AbstractFixture implements ContainerAwareInterface
{
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /** @var \Vivo\SiteBundle\Repository\SiteRepositoryInterface $siteRepository */
        $siteRepository = $this->container->get('vivo_site.repository.site');
        /** @var \Vivo\SiteBundle\Repository\DomainRepositoryInterface $domainRepository */
        $domainRepository = $this->container->get('vivo_site.repository.domain');

        $domain = $domainRepository->createDomain();
        $domain->setHost('localhost')
            ->setPrimary(true);

        $site = $siteRepository->createSite();
        $site->setName('Site')
            ->setSenderEmail('noreply@localhost.com')
            ->setPrimary(true)
            ->addDomain($domain);

        $manager->persist($domain);
        $manager->persist($site);
        $manager->flush();
    }
}
