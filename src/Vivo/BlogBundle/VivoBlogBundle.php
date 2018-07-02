<?php

namespace Vivo\BlogBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class VivoBlogBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Vivo\BlogBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.default_entity_manager')));
        $container->addCompilerPass(new EntityInheritancePass('%vivo_blog.model.category_slug%', '%vivo_slug.model.slug%', 'vivo_blog_category'));
        $container->addCompilerPass(new EntityInheritancePass('%vivo_blog.model.post_slug%', '%vivo_slug.model.slug%', 'vivo_blog_post'));
    }
}
