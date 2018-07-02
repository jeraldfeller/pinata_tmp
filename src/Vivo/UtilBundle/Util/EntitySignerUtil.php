<?php

namespace Vivo\UtilBundle\Util;

use Doctrine\ORM\EntityManagerInterface;

class EntitySignerUtil
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var array
     */
    private $cache = array();

    /**
     * EntitySignerUtil constructor.
     *
     * @param EntityManagerInterface $em
     * @param                        $secret
     */
    public function __construct(EntityManagerInterface $em, $secret)
    {
        $this->em = $em;
        $this->secret = $secret;
    }

    /**
     * @param object $entity
     *
     * @return string
     */
    public function getSignedId($entity)
    {
        $entityId = $entity->getId();
        $entityClassName = $this->getNormalizedClassName($entity);

        return $entityId.':'.$this->generateHash($entityClassName, $entityId);
    }

    /**
     * @param string $entityClass
     * @param string $signedId
     *
     * @return null|object
     */
    public function findEntityBySignedId($entityClass, $signedId)
    {
        list($id, $userHash) = array_pad(explode(':', $signedId, 2), 2, '');
        $entityClassName = $this->getNormalizedClassName($entityClass);
        $hash = $this->generateHash($entityClassName, $id);

        if (!hash_equals($hash, $userHash)) {
            return;
        }

        return $this->em->getRepository($entityClassName)
            ->find($id);
    }

    /**
     * @param string $entityClass
     * @param int    $entityId
     *
     * @return string
     */
    private function generateHash($entityClass, $entityId)
    {
        $entityId = (int) $entityId;

        if (isset($this->cache[$entityClass.'_'.$entityId])) {
            return $this->cache[$entityClass.'_'.$entityId];
        }

        return $this->cache[$entityClass.'_'.$entityId] = hash('sha256', $entityClass.$entityId.$this->secret);
    }

    /**
     * @param string|object $entity
     *
     * @return string
     */
    private function getNormalizedClassName($entity)
    {
        return $this->em->getClassMetadata(is_object($entity) ? get_class($entity) : $entity)
            ->getName();
    }
}
