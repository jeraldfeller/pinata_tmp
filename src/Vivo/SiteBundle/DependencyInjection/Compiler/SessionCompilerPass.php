<?php

namespace Vivo\SiteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SessionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasAlias('session.storage.native') && false === $container->hasDefinition('session.storage.native')) {
            return;
        }

        $definition = $container->getDefinition('session.storage.native');
        $definition->setClass('Vivo\SiteBundle\Session\Storage');
        $definition->addMethodCall('setRequestStack', array(new Reference('request_stack')));
    }
}
