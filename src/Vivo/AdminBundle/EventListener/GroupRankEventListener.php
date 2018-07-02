<?php

namespace Vivo\AdminBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Vivo\AdminBundle\Model\GroupInterface;

class GroupRankEventListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof GroupInterface) {
            if (!$entity->getRank()) {
                $groupRepository = $args->getEntityManager()->getRepository(get_class($entity));

                /** @var \Vivo\AdminBundle\Model\GroupInterface $highestGroup */
                $highestGroup = $groupRepository->createQueryBuilder('user_group')
                    ->orderBy('user_group.rank', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getSingleResult();

                $entity->setRank($highestGroup->getRank() + 1);
            }
        }
    }
}
