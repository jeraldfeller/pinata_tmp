<?php

namespace Vivo\PageBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;

class LoadPageTypeListener
{
    /**
     * @var \Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface
     */
    protected $pageTypeManager;

    /**
     * @param PageTypeManagerInterface $pageTypeManager
     */
    public function __construct(PageTypeManagerInterface $pageTypeManager)
    {
        $this->pageTypeManager = $pageTypeManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof PageInterface) {
            $pageType = $this->pageTypeManager->getPageTypeInstanceByAlias($entity->getPageType());

            $entity->setPageTypeInstance($pageType);
        }
    }
}
