<?php

namespace Vivo\UtilBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Vivo\UtilBundle\Util\ClassAnalyzerInterface;

class PrimaryListener
{
    protected $classAnalyzer;

    public function __construct(ClassAnalyzerInterface $classAnalyzer)
    {
        $this->classAnalyzer = $classAnalyzer;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->processPrimaryEntity($args);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->processPrimaryEntity($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function processPrimaryEntity(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();

        if ($this->isEntitySupported($entity)) {
            if ($args instanceof PreUpdateEventArgs) {
                if (!$args->hasChangedField('primary')) {
                    return;
                }
            }

            /** @var \Vivo\UtilBundle\Behaviour\Repository\PrimaryRepositoryTrait $repository */
            $repository = $em->getRepository(get_class($entity));

            $repository->updatePrimaryEntity($entity);
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

        if (!$metadata->hasField('primary')) {
            $metadata->mapField(array(
                'fieldName' => 'primary',
                'columnName' => $eventArgs->getEntityManager()->getConfiguration()->getNamingStrategy() instanceof UnderscoreNamingStrategy ? 'is_primary' : 'isPrimary',
                'type' => 'boolean',
                'nullable' => true,
            ));
        }
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    protected function isEntitySupported($entity)
    {
        return $this->classAnalyzer->hasTrait(new \ReflectionClass(get_class($entity)), self::getTrait());
    }

    /**
     * Get trait name.
     *
     * @return string
     */
    protected static function getTrait()
    {
        return 'Vivo\UtilBundle\Behaviour\Model\PrimaryTrait';
    }
}
