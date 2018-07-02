<?php

namespace Vivo\UtilBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Vivo\UtilBundle\Util\EntitySignerUtil;

class SecureEntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var EntitySignerUtil
     */
    protected $entitySigner;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var bool
     */
    protected $multiple;

    /**
     * @param EntitySignerUtil $entitySigner
     * @param string           $class
     * @param bool             $multiple
     */
    public function __construct(EntitySignerUtil $entitySigner, $class, $multiple)
    {
        $this->entitySigner = $entitySigner;
        $this->class = $class;
        $this->multiple = (bool) $multiple;
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
                $ids[] = $this->entitySigner->getSignedId($entity);
            }

            return $ids;
        }

        return $this->entitySigner->getSignedId($entities);
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
                $entities[] = $this->findEntityBySignedId($id);
            }

            return $entities;
        }

        return $this->findEntityBySignedId($ids);
    }

    /**
     * @param string $signedId
     *
     * @return object
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    protected function findEntityBySignedId($signedId)
    {
        $entity = $this->entitySigner->findEntityBySignedId($this->class, $signedId);

        if (!$entity) {
            throw new TransformationFailedException();
        }

        return $entity;
    }
}
