<?php

namespace App\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class AppCoreBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EntityInheritancePass('App\CoreBundle\Entity\FarmSlug', '%vivo_slug.model.slug%', 'app_core_farm'));
        $container->addCompilerPass(new EntityInheritancePass('App\CoreBundle\Entity\FarmImage', '%vivo_asset.model.asset%', 'app_core_farm_image'));

        $container->addCompilerPass(new EntityInheritancePass('App\CoreBundle\Entity\FruitSlug', '%vivo_slug.model.slug%', 'app_core_fruit'));
        $container->addCompilerPass(new EntityInheritancePass('App\CoreBundle\Entity\FruitImage', '%vivo_asset.model.asset%', 'app_core_fruit_image'));
    }


}
