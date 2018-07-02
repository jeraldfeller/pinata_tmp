<?php

namespace Vivo\UtilBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class MappedSuperclassListener implements EventSubscriber
{
    /**
     * @var array
     */
    protected $classes;

    /**
     * Constructor.
     *
     * @param array $classes
     */
    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();
        $configuration = $eventArgs->getEntityManager()->getConfiguration();

        foreach ($this->classes as $class) {
            if (array_key_exists('model', $class) && $class['model'] === $metadata->getName()) {
                $metadata->isMappedSuperclass = false;

                if (array_key_exists('repository', $class)) {
                    $metadata->setCustomRepositoryClass($class['repository']);
                }
            }
        }

        if (!$metadata->isMappedSuperclass) {
            $parentClasses = class_parents($metadata->getName());

            foreach ($parentClasses as $parent) {
                $parentMetadata = new ClassMetadata(
                    $parent,
                    $configuration->getNamingStrategy()
                );

                if (in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                    $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);
                    if ($parentMetadata->isMappedSuperclass) {
                        foreach ($parentMetadata->getAssociationMappings() as $key => $value) {
                            if (ClassMetadataInfo::ONE_TO_MANY === $value['type'] ||
                                ClassMetadataInfo::ONE_TO_ONE === $value['type'] ||
                                ClassMetadataInfo::MANY_TO_MANY === $value['type']) {
                                $metadata->associationMappings[$key] = $value;
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($metadata->getAssociationMappings() as $key => $value) {
                if ($value['type'] === ClassMetadataInfo::ONE_TO_MANY ||
                    $value['type'] === ClassMetadataInfo::ONE_TO_ONE ||
                    $value['type'] === ClassMetadataInfo::MANY_TO_MANY) {
                    unset($metadata->associationMappings[$key]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }
}
