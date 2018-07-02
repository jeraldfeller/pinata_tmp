<?php

namespace Vivo\UtilBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntityInheritancePass implements CompilerPassInterface
{
    protected $child;

    protected $parent;

    protected $alias;

    public function __construct($child, $parent, $alias)
    {
        $this->child = $child;
        $this->parent = $parent;
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $inheritanceMap = $container->hasParameter('vivo_util.entity_inheritance_map') ? $container->getParameter('vivo_util.entity_inheritance_map') : array();

        $inheritanceMap[] = array(
            'child' => $this->child,
            'parent' => $this->parent,
            'alias' => $this->alias,
        );

        $container->setParameter('vivo_util.entity_inheritance_map', $inheritanceMap);
    }
}
