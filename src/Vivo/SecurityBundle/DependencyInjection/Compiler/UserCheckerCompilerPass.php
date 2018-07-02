<?php

namespace Vivo\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class UserCheckerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasAlias('security.user_checker') && false === $container->hasDefinition('security.user_checker')) {
            return;
        }

        $definition = $container->findDefinition('security.user_checker');
        $definition->setClass('Vivo\\SecurityBundle\\Security\\UserChecker');
        $definition->addMethodCall('setFirewallManager', array(new Reference('vivo_security.util.firewall_manager')));
        $definition->addMethodCall('setAuthenticationLogRepository', array(new Reference('vivo_security.repository.authentication_log')));
        $definition->addMethodCall('setRequestStack', array(new Reference('request_stack')));
        $definition->addMethodCall('setTokenStorage', array(new Reference('security.token_storage')));

        if (true === $container->hasAlias('logger') || true === $container->hasDefinition('logger')) {
            $definition->addMethodCall('setLogger', array(new Reference('logger')));
        }
    }
}
