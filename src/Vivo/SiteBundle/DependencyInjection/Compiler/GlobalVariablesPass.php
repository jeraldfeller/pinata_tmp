<?php

namespace Vivo\SiteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GlobalVariablesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasAlias('twig.app_variable') && false === $container->hasDefinition('twig.app_variable')) {
            $container->setParameter('templating.globals.class', 'Vivo\\SiteBundle\\Templating\\GlobalVariables');
        } else {
            $definition = $container->getDefinition('twig.app_variable');
            $definition->setClass('Vivo\SiteBundle\Templating\AppVariable');

            $definition->addMethodCall('setSiteFactory', array(new Reference('vivo_site.factory.site')));
            $definition->addMethodCall('setSeo', array(new Reference('vivo_site.seo.page')));
        }
    }
}
