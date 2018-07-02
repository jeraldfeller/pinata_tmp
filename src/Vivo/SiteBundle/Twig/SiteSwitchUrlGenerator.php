<?php

namespace Vivo\SiteBundle\Twig;

use Symfony\Component\Routing\RouterInterface;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Model\SiteInterface;

class SiteSwitchUrlGenerator extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @param RouterInterface $router
     * @param SiteFactory     $siteFactory
     */
    public function __construct(RouterInterface $router, SiteFactory $siteFactory)
    {
        $this->router = $router;
        $this->siteFactory = $siteFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('site_path', array($this, 'generateUrl')),
        );
    }

    /**
     * @return string
     */
    public function generateUrl(SiteInterface $site, $route, $parameters)
    {
        if ($this->siteFactory->get() === $site) {
            return $this->router->generate($route, $parameters);
        }

        $parameters = array(
            'id' => $site->getId(),
            'route' => $route,
            'route_params' => $parameters,
        );

        return $this->router->generate('vivo_site.admin.site.switch', $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_site_site_switch_url_generator';
    }
}
