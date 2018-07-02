<?php

namespace Vivo\UtilBundle\Twig;

use Vivo\UtilBundle\Util\EntitySignerUtil;

class EntitySignerExtension extends \Twig_Extension
{
    /**
     * @var EntitySignerUtil
     */
    protected $entitySigner;

    /**
     * @param EntitySignerUtil $entitySigner
     */
    public function __construct(EntitySignerUtil $entitySigner)
    {
        $this->entitySigner = $entitySigner;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('vivo_util_signed_entity_id', array($this, 'getSignedId')),
        );
    }

    /**
     * @param object $entity
     *
     * @return string
     */
    public function getSignedId($entity)
    {
        return $this->entitySigner->getSignedId($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_util_entity_signer';
    }
}
