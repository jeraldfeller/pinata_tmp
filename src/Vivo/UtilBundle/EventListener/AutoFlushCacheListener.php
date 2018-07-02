<?php

namespace Vivo\UtilBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;

class AutoFlushCacheListener
{
    /**
     * @var bool
     */
    protected $clearCacheOnPostFlush;

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates(),
            $uow->getScheduledEntityDeletions()
        );

        foreach ($entities as $entity) {
            if ($this->isCachedEntity($entity)) {
                $this->clearCacheOnPostFlush = true;

                return;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (true === $this->clearCacheOnPostFlush) {
            $this->clearCache($args);
            $this->clearCacheOnPostFlush = false;
        }
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    protected function isCachedEntity($entity)
    {
        return $entity instanceof AutoFlushCacheInterface ? true : false;
    }

    /**
     * @param OnFlushEventArgs $args
     */
    protected function clearCache(PostFlushEventArgs $args)
    {
        /** @var \Doctrine\Common\Cache\CacheProvider $cacheDriver */
        $cacheDriver = $args->getEntityManager()
            ->getConfiguration()
            ->getResultCacheImpl();

        $cacheDriver->deleteAll();
    }
}
