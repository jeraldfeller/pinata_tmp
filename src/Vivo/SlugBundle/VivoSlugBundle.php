<?php

namespace Vivo\SlugBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class VivoSlugBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Vivo\SlugBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.default_entity_manager')));
        $container->addCompilerPass(new EntityInheritancePass('%vivo_slug.model.slug%', '%vivo_slug.model.slug%', 'vivo_slug'));
    }
}
