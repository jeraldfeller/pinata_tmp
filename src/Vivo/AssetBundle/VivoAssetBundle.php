<?php

namespace Vivo\AssetBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class VivoAssetBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Vivo\AssetBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.default_entity_manager')));
        $container->addCompilerPass(new EntityInheritancePass('%vivo_asset.model.ckeditor_asset%', '%vivo_asset.model.asset%', 'vivo_asset_ckeditor'));
    }
}
