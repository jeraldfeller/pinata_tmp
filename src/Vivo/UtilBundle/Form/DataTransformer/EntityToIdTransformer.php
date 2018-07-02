<?php

namespace Vivo\UtilBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var bool
     */
    protected $multiple;

    /**
     * @param ObjectManager $objectManager
     * @param string        $class
     * @param bool          $multiple
     */
    public function __construct(ObjectManager $objectManager, $class, $multiple = false)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
        $this->multiple = $multiple;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($entities)
    {
        if (null === $entities) {
            return;
        }

        if ($this->multiple) {
            $ids = array();
            foreach ($entities as $entity) {
                $ids[] = $entity->getId();
            }

            return $ids;
        }

        return $entities->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($ids)
    {
        if (!$ids) {
            return;
        }

        if ($this->multiple) {
            $entities = array();

            foreach ($ids as $id) {
                $entities[] = $this->getEntityFromId($id);
            }

            return $entities;
        }

        return $this->getEntityFromId($ids);
    }

    /**
     * @param $id
     *
     * @return object
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    protected function getEntityFromId($id)
    {
        $entity = $this->objectManager
            ->getRepository($this->class)
            ->find($id);

        if (null === $entity) {
            throw new TransformationFailedException();
        }

        return $entity;
    }
}
