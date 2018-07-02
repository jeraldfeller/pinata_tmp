<?php

namespace Vivo\SiteBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vivo\SiteBundle\Doctrine\FilterAware\SiteAwareInterface;

class SitePersistListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof SiteAwareInterface) {
            if (null === $entity->getSite()) {
                $entity->setSite($this->container->get('vivo_site.factory.site')->get());
            }
        }
    }
}
