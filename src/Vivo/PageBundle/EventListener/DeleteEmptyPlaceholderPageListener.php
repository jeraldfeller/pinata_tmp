<?php

namespace Vivo\PageBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Vivo\PageBundle\Model\MenuNodeInterface;
use Vivo\PageBundle\PageType\Type\PlaceholderPageType;

class DeleteEmptyPlaceholderPageListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof MenuNodeInterface) {
            if ($entity->getPage() && $entity->getPage()->getPageTypeInstance() instanceof PlaceholderPageType) {
                foreach ($entity->getPage()->getMenuNodes() as $menuNode) {
                    if ($menuNode !== $entity) {
                        // Menu has another menu node available
                        return;
                    }
                }

                $args->getEntityManager()->remove($entity->getPage());
            }
        }
    }
}
