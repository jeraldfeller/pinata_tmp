<?php

namespace Vivo\SiteBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\SiteBundle\DependencyInjection\Compiler\GlobalVariablesPass;
use Vivo\SiteBundle\DependencyInjection\Compiler\SessionCompilerPass;
use Vivo\SiteBundle\DependencyInjection\Compiler\SitemapCompilerPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

class VivoSiteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Vivo\SiteBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.default_entity_manager')));
        $container->addCompilerPass(new SessionCompilerPass());
        $container->addCompilerPass(new GlobalVariablesPass());
        $container->addCompilerPass(new SitemapCompilerPass());
    }
}
