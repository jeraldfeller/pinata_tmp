<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Vivo\TwitterBootstrapBundle\VivoTwitterBootstrapBundle(),
            new Vivo\UtilBundle\VivoUtilBundle(),
            new Vivo\PageBundle\VivoPageBundle(),
            new Vivo\BlogBundle\VivoBlogBundle(),
            new Vivo\TreeBundle\VivoTreeBundle(),
            new Vivo\SiteBundle\VivoSiteBundle(),
            new Vivo\AdminBundle\VivoAdminBundle(),
            new Vivo\SecurityBundle\VivoSecurityBundle(),
            new Vivo\AssetBundle\VivoAssetBundle(),
            new Vivo\SlugBundle\VivoSlugBundle(),
            new Vivo\AddressBundle\VivoAddressBundle(),

            new App\CoreBundle\AppCoreBundle(),
            new App\FaqBundle\AppFaqBundle(),
            new App\SiteBundle\AppSiteBundle(),
            new App\TeamBundle\AppTeamBundle(),
            new App\BlogBundle\AppBlogBundle(),

            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Trsteel\CkeditorBundle\TrsteelCkeditorBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Trsteel\HtmlFormValidationBundle\TrsteelHtmlFormValidationBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
