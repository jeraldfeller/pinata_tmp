<?php

namespace Vivo\UtilBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class NormalizeDateTimeListener
{
    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $zone = new \DateTimeZone(date_default_timezone_get());

        //Get hold of the entities that are scheduled for insertion or update
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($entities as $entity) {
            $reflection = new \ReflectionClass($entity);
            foreach ($reflection->getProperties() as $prop) {
                if ($prop->isStatic()) {
                    continue;
                }

                try {
                    $value = $accessor->getValue($entity, $prop->getName());

                    if ($value instanceof \DateTime) {
                        if ($value->getTimezone()->getName() !== $zone->getName()) {
                            $value->setTimezone($zone);

                            $this->recomputeEntity($em, $entity);
                        }
                    }
                } catch (AccessException $e) {
                }
            }
        }
    }

    /**
     * @param EntityManager $em
     * @param $entity
     */
    protected function recomputeEntity(EntityManager $em, $entity)
    {
        $uow = $em->getUnitOfWork();
        $meta = $em->getClassMetadata(get_class($entity));

        if (count($uow->getEntityChangeSet($entity)) > 0) {
            // There are already changes - We can just recompute
            $uow->recomputeSingleEntityChangeSet($meta, $entity);
        } else {
            // There are no change sin the changeset - Initialise the computation
            $uow->computeChangeSet($meta, $entity);
        }
    }
}
