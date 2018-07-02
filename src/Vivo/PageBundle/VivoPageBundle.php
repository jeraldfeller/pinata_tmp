<?php

namespace Vivo\PageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vivo\PageBundle\DependencyInjection\Compiler\PageTypeCompilerPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class VivoPageBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Vivo\PageBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.default_entity_manager')));
        $container->addCompilerPass(new PageTypeCompilerPass());
        $container->addCompilerPass(new EntityInheritancePass('%vivo_page.model.slug%', '%vivo_slug.model.slug%', 'vivo_page'));
        $container->addCompilerPass(new EntityInheritancePass('%vivo_page.model.asset%', '%vivo_asset.model.asset%', 'vivo_page_asset'));
    }
}
