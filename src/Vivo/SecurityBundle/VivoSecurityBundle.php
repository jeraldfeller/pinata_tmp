<?php

namespace Vivo\SecurityBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Vivo\SecurityBundle\DependencyInjection\Compiler\FirewallManagerCompilerPass;
use Vivo\SecurityBundle\DependencyInjection\Compiler\UserCheckerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class VivoSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Vivo\SecurityBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.default_entity_manager')));
        $container->addCompilerPass(new FirewallManagerCompilerPass());
        $container->addCompilerPass(new UserCheckerCompilerPass());
    }
}
