<?php

namespace Vivo\SiteBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Vivo\UtilBundle\Util\ClassAnalyzerInterface;

class SiteTraitListener implements EventSubscriber
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

        if (!$metadata->hasAssociation('site')) {
            $metadata->mapManyToOne(array(
                'fieldName' => 'site',
                'targetEntity' => 'Vivo\SiteBundle\Model\SiteInterface',
                'joinColumns' => array(
                    array(
                        'nullable' => true,
                        'onDelete' => 'RESTRICT',
                    ),
                ),
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
        return 'Vivo\SiteBundle\Behaviour\SiteTrait';
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
