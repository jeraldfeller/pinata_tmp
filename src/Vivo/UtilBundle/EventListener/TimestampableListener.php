<?php

namespace Vivo\UtilBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Vivo\UtilBundle\Util\ClassAnalyzerInterface;

class TimestampableListener
{
    /**
     * @var \Vivo\UtilBundle\Util\ClassAnalyzerInterface
     */
    protected $classAnalyzer;

    /**
     * @param ClassAnalyzerInterface $classAnalyzer
     */
    public function __construct(ClassAnalyzerInterface $classAnalyzer)
    {
        $this->classAnalyzer = $classAnalyzer;
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $deletedEntities = $uow->getScheduledEntityDeletions();
        $updateEntities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($updateEntities as $entity) {
            $this->updateEntity($em, $entity, $deletedEntities);
        }

        foreach ($deletedEntities as $entity) {
            $this->cascadeUpdate($em, $entity, new \DateTime('now'), $deletedEntities);
        }
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        if (null === $metadata->getReflectionClass() || !$this->classAnalyzer->hasTrait($metadata->getReflectionClass(), self::getTrait())) {
            return;
        }

        if (!$metadata->hasField('createdAt')) {
            $metadata->mapField(array(
                'fieldName' => 'createdAt',
                'type' => 'datetime',
                'nullable' => true,
            ));

            $columnName = $metadata->getColumnName('createdAt');
            $builder = new ClassMetadataBuilder($metadata);
            $builder->addIndex(array($columnName), $columnName);
        }

        if (!$metadata->hasField('updatedAt')) {
            $metadata->mapField(array(
                'fieldName' => 'updatedAt',
                'type' => 'datetime',
                'nullable' => true,
            ));

            $columnName = $metadata->getColumnName('updatedAt');
            $builder = new ClassMetadataBuilder($metadata);
            $builder->addIndex(array($columnName), $columnName);
        }
    }

    /**
     * @param EntityManager                                       $em
     * @param \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait $entity
     * @param array                                               $ignoreEntities
     */
    protected function updateEntity(EntityManager $em, $entity, array $ignoreEntities = null)
    {
        if ($this->isEntitySupported($entity)) {
            $uow = $em->getUnitOfWork();

            $ignoreFields = array_merge($entity->getIgnoredUpdateFields(), array('updatedAt', 'createdAt'));
            $totalFieldsChanged = 0;

            foreach ($uow->getEntityChangeSet($entity) as $field => $value) {
                if (!in_array($field, $ignoreFields)) {
                    ++$totalFieldsChanged;
                }
            }

            if ($totalFieldsChanged > 0) {
                $changeset = $uow->getEntityChangeSet($entity);

                if ((!isset($changeset['createdAt']) || (null === $changeset['createdAt'][0] && null === $changeset['createdAt'][1])) && null === $entity->getCreatedAt()) {
                    $entity->setCreatedAt(new \DateTime('now'));
                }

                // Check if updatedAt was updated manually
                if (!isset($changeset['updatedAt']) || (null === $changeset['updatedAt'][0] && null === $changeset['updatedAt'][1])) {
                    $entity->setUpdatedAt(new \DateTime('now'));
                }

                if (null === $ignoreEntities || !in_array($entity, $ignoreEntities, true)) {
                    $this->recomputeEntity($em, $entity);
                }

                $this->cascadeUpdate($em, $entity, $entity->getUpdatedAt());
            }
        }
    }

    /**
     * @param EntityManager                                       $em
     * @param \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait $entity
     * @param \DateTime                                           $datetime
     * @param array                                               $ignoreEntities
     */
    protected function cascadeUpdate(EntityManager $em, $entity, \DateTime $datetime, array $ignoreEntities = null)
    {
        if (!$this->isEntitySupported($entity)) {
            return;
        }

        foreach ($entity->getCascadedTimestampableFields() as $cascadeEntity) {
            if ($this->isEntitySupported($cascadeEntity)) {
                $cascadeEntity->setUpdatedAt($datetime);

                if (null === $ignoreEntities || !in_array($cascadeEntity, $ignoreEntities, true)) {
                    $this->recomputeEntity($em, $cascadeEntity);
                }

                $this->cascadeUpdate($em, $cascadeEntity, $datetime, $ignoreEntities);
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

    /**
     * @param $entity
     *
     * @return bool
     */
    protected function isEntitySupported($entity)
    {
        if (!$entity) {
            return false;
        }

        return $this->classAnalyzer->hasTrait(new \ReflectionClass($entity), self::getTrait());
    }

    /**
     * Get trait name.
     *
     * @return string
     */
    protected static function getTrait()
    {
        return 'Vivo\UtilBundle\Behaviour\Model\TimestampableTrait';
    }
}
