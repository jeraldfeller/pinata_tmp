<?php

namespace App\BlogBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class AppBlogBundle extends Bundle
{
    public function getParent()
    {
        return 'VivoBlogBundle';
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EntityInheritancePass('App\BlogBundle\Entity\PostImage', '%vivo_asset.model.asset%', 'vivo_blog_asset'));
    }
}
