<?php

namespace Vivo\TreeBundle\EventListener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Vivo\TreeBundle\Model\TreeInterface;

/**
 * Class FixParentLoopListener.
 *
 * @TODO: Update so that we don't need to re-flush
 */
class FixParentLoopListener
{
    /**
     * @var array
     */
    protected $oldValues;

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof TreeInterface) {
            if ($args->hasChangedField('parent')) {
                $parent = $args->getNewValue('parent');

                if ($args->getNewValue('parent') === $entity) {
                    // Do not allow a parent of a parent
                    $parent = $args->getOldValue('parent');
                    $args->setNewValue('parent', $parent);
                }

                if ($parent !== $args->getOldValue('parent')) {
                    $this->oldValues[] = array(
                        'entity' => $entity,
                        'oldParent' => $args->getOldValue('parent'),
                        'firstChild' => $entity->getChildren()->first() ?: null,
                    );
                }
            }
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (is_array($this->oldValues)) {
            $em = $args->getEntityManager();
            $fixEntities = array();

            foreach ($this->oldValues as $values) {
                /** @var \Vivo\TreeBundle\Repository\TreeRepositoryInterface $repo */
                $repo = $em->getRepository(get_class($values['entity']));
                $newTree = $repo->getFlatArrayChildren();

                $missingEntity = true;
                foreach ($newTree as $treeLevel) {
                    if ($treeLevel->getModel() === $values['entity']) {
                        $missingEntity = false;
                    }
                }

                if ($missingEntity) {
                    if ($fixParent = $values['firstChild']) {
                        $fixParent->setParent($values['oldParent']);

                        $fixEntities[] = $fixParent;
                    }
                }
            }

            if (count($fixEntities) > 0) {
                $em->flush($fixEntities);
            }

            $this->oldValues = null;
        }
    }
}
