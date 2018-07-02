<?php

namespace Vivo\UtilBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Vivo\UtilBundle\Util\ClassAnalyzerInterface;

class SeoMetaListener
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
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        if (null === $metadata->getReflectionClass() || !$this->classAnalyzer->hasTrait($metadata->getReflectionClass(), self::getTrait())) {
            return;
        }

        if (!$metadata->hasField('metaTitle')) {
            $metadata->mapField(array(
                'fieldName' => 'metaTitle',
                'type' => 'string',
                'length' => 255,
                'nullable' => true,
            ));
        }

        if (!$metadata->hasField('metaDescription')) {
            $metadata->mapField(array(
                'fieldName' => 'metaDescription',
                'type' => 'string',
                'length' => 255,
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
        return $this->classAnalyzer->hasTrait(new \ReflectionClass($entity), self::getTrait());
    }

    /**
     * Get trait name.
     *
     * @return string
     */
    protected static function getTrait()
    {
        return 'Vivo\UtilBundle\Behaviour\Model\SeoMetaTrait';
    }
}
