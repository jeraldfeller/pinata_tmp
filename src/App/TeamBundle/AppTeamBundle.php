<?php

namespace App\TeamBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class AppTeamBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EntityInheritancePass('App\TeamBundle\Entity\ProfileImage', '%vivo_asset.model.asset%', 'app_team_profile_image'));
        $container->addCompilerPass(new EntityInheritancePass('App\TeamBundle\Entity\ProfileSlug', '%vivo_slug.model.slug%', 'app_team_profile'));
    }
}
