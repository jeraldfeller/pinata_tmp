<?php

namespace Vivo\SiteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SitemapCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasAlias('vivo_site.seo.sitemap.manager') && false === $container->hasDefinition('vivo_site.seo.sitemap.manager')) {
            return;
        }

        $taggedServiceIds = $container->findTaggedServiceIds('vivo_site.seo.sitemap');
        $definition = $container->findDefinition('vivo_site.seo.sitemap.manager');

        foreach ($taggedServiceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $definition->addMethodCall('addSitemap', array(new Reference($serviceId)));
            }
        }
    }
}
