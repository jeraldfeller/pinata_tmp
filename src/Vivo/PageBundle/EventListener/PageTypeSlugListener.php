<?php

namespace Vivo\PageBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Vivo\PageBundle\Model\PageInterface;

/**
 * Class PageTypeSlugListener.
 *
 * This ensures that a PageType has the correct slug if it is forced on a PageType
 *  - An example of this is the homepage - It is usually forced to "/"
 */
class PageTypeSlugListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->checkPageEntity($args);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->checkPageEntity($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function checkPageEntity(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof PageInterface && $entity->getPageTypeInstance() && $entity->getPrimarySlug()) {
            if (!$entity->getPageTypeInstance()->getRouteName() || $entity->getPageTypeInstance()->getSlug() !== $entity->getPrimarySlug()->getSlug()) {
                $entity->setPrimarySlug(null);

                $em = $args->getEntityManager();
                $uow = $em->getUnitOfWork();
                $meta = $em->getClassMetadata(get_class($entity));
                $uow->recomputeSingleEntityChangeSet($meta, $entity);
            }
        }
    }
}
