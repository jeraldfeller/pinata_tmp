<?php

namespace Vivo\AssetBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Vivo\AssetBundle\Model\AssetInterface;
use Vivo\UtilBundle\Util\Urlizer;

/**
 * AssetDefaultsEventListener.
 */
class AssetDefaultsEventListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AssetInterface) {
            if (strlen($entity->getFilename()) < 1 && $entity->getFile()) {
                $filename = (Urlizer::urlize($entity->getTitle()) ?: 'untitled').'.'.$entity->getFile()->getExtension();
                $entity->setFilename($filename);
            }
        }
    }

    /**
     * Store the uploaded file.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AssetInterface) {
            if (strlen($entity->getFilename()) < 1 && $entity->getFile()) {
                $filename = (Urlizer::urlize($entity->getTitle()) ?: 'untitled').'.'.$entity->getFile()->getExtension();
                $entity->setFilename($filename);
            }
        }
    }
}
