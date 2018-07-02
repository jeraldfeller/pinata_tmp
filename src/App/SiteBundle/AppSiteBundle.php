<?php

namespace App\SiteBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vivo\UtilBundle\DependencyInjection\Compiler\EntityInheritancePass;

class AppSiteBundle extends Bundle
{
    public function getParent()
    {
        return 'VivoSiteBundle';
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EntityInheritancePass('App\SiteBundle\Entity\SiteLogo', '%vivo_asset.model.asset%', 'app_site_logo'));
    }
}
