<?php

namespace Vivo\UtilBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class InheritanceListener implements EventSubscriber
{
    /**
     * @var array
     */
    protected $classes;

    /**
     * @var array
     */
    protected $resolvedClasses;

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
     * @param LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metaData */
        $metaData = $args->getClassMetadata();

        foreach ($this->getResolvedClasses() as $parent => $children) {
            foreach ($children as $child => $alias) {
                $reflection = new \ReflectionClass($parent);

                if ($metaData->getName() === $reflection->getName()) {
                    $metaData->addDiscriminatorMapClass($alias, $child);
                }
            }
        }
    }

    /**
     * Get the resolved class map.
     *
     * @return array
     */
    protected function getResolvedClasses()
    {
        if (null !== $this->resolvedClasses) {
            return $this->resolvedClasses;
        }

        $classes = array();

        foreach ($this->classes as $class) {
            $parentReflection = new \ReflectionClass($class['parent']);
            $childReflection = new \ReflectionClass($class['child']);

            $classes = array_merge_recursive($classes, $this->getClasses($parentReflection, $childReflection, $class['alias']));
        }

        return $this->resolvedClasses = $classes;
    }

    /**
     * @param \ReflectionClass $parent
     * @param \ReflectionClass $child
     * @param $alias
     *
     * @return array
     */
    private function getClasses(\ReflectionClass $parent, \ReflectionClass $child, $alias)
    {
        $classes[$parent->getName()][$child->getName()] = $alias;

        if ($parent = $parent->getParentClass()) {
            $classes = array_merge_recursive($classes, $this->getClasses($parent, $child, $alias));
        }

        return $classes;
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
