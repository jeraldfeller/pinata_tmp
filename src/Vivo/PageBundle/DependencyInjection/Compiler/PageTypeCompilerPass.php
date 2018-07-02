<?php

namespace Vivo\PageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PageTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasAlias('vivo_page.manager.page_type') && false === $container->hasDefinition('vivo_page.manager.page_type')) {
            return;
        }

        $taggedServiceIds = $container->findTaggedServiceIds('vivo_page.page_type');

        $definition = $container->findDefinition('vivo_page.manager.page_type');

        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                $definition->addMethodCall('addPageType', array(new Reference($serviceId), (string) $tagAttributes['alias']));
            }
        }
    }
}
