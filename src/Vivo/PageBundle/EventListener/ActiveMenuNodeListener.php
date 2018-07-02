<?php

namespace Vivo\PageBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Vivo\PageBundle\Model\MenuNodeInterface;
use Vivo\PageBundle\Model\PageInterface;

class ActiveMenuNodeListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $checkMenus = array();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof MenuNodeInterface) {
                $checkMenus[] = $entity->getMenu() ?: $entity;
            } elseif ($entity instanceof PageInterface) {
                foreach ($entity->getMenuNodes() as $node) {
                    $checkMenus[] = $node->getMenu() ?: $node;
                }
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $changeset = $uow->getEntityChangeSet($entity);

            if ($entity instanceof MenuNodeInterface) {
                if (
                    isset($changeset['menu'])
                    || isset($changeset['parent'])
                    || isset($changeset['rank'])
                    || isset($changeset['disabled'])
                ) {
                    if (isset($changeset['menu'])) {
                        // menu has changed - let's check the old menu as well
                        if ($oldValue = reset($changeset['menu'])) {
                            $checkMenus[] = $oldValue->getMenu() ?: $oldValue;
                        }
                    }

                    $checkMenus[] = $entity->getMenu() ?: $entity;
                }
            } elseif ($entity instanceof PageInterface) {
                foreach ($entity->getMenuNodes() as $node) {
                    $checkMenus[] = $node->getMenu() ?: $node;
                }
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof MenuNodeInterface) {
                // Always get the parent if one exists as it
                // may be a placeholder and no children
                $checkMenus[] = $entity->getMenu() ?: $entity;
            } elseif ($entity instanceof PageInterface) {
                foreach ($entity->getMenuNodes() as $node) {
                    $checkMenus[] = $node->getMenu() ?: $node;
                }
            }
        }

        $checkMenus = $this->uniqueArrayViaSplObjectHash($checkMenus);

        $this->calculateDisabled($checkMenus, $em);
    }

    /**
     * Recalculate if this entity should be disabled.
     *
     * @param MenuNodeInterface[] $menus
     * @param EntityManager       $em
     */
    protected function calculateDisabled(array $menus, EntityManager $em)
    {
        // set all disabled to false
        foreach ($menus as $menu) {
            $this->setMenuNodeAndChildrenDisabled($menu, $em, false);
        }

        foreach ($menus as $menu) {
            $this->setMenuNodeAndChildrenDisabled($menu, $em);
        }
    }

    protected function setMenuNodeAndChildrenDisabled(MenuNodeInterface $menuNode, EntityManager $em, $disabled = null)
    {
        $disabledState = $disabled;

        if (null === $disabled) {
            // calculate disabled state
            $disabledState = false;

            if (null !== $menuNode->getPage() && $menuNode->getPage()->isDisabled()) {
                $disabledState = true;
            } elseif (null !== $menuNode->getParent()) {
                $disabledState = $menuNode->getParent()->isDisabled();
            }

            if (!$disabledState && ($menuNode->getParent() && !$menuNode->getRouteName())) {
                $disabledState = true;
            }
        }

        $menuNode->setDisabled($disabledState);
        $this->recompute($menuNode, $em);

        if (true === $disabledState) {
            // If the disabled state is true, all children should automatically disable without any calculation
            $disabled = true;
        }

        foreach ($menuNode->getChildren() as $child) {
            $this->setMenuNodeAndChildrenDisabled($child, $em, $disabled);
        }

        return $this;
    }

    /**
     * @param MenuNodeInterface $menuNode
     * @param EntityManager     $em
     */
    protected function recompute(MenuNodeInterface $menuNode, EntityManager $em)
    {
        $uow = $em->getUnitOfWork();
        $meta = $em->getClassMetadata(get_class($menuNode));

        if (count($uow->getEntityChangeSet($menuNode)) > 0) {
            // There are already changes - We can just recompute
            $uow->recomputeSingleEntityChangeSet($meta, $menuNode);
        } else {
            // There are no change sin the changeset - Initialise the computation
            $uow->computeChangeSet($meta, $menuNode);
        }
    }

    /**
     * Remove duplicate objects from array.
     *
     * @param array $data
     *
     * @return array
     */
    protected function uniqueArrayViaSplObjectHash(array $data)
    {
        $results = array();

        foreach ($data as $result) {
            $results[spl_object_hash($result)] = $result;
        }

        return $results;
    }
}
