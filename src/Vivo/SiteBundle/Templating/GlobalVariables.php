<?php

namespace Vivo\SiteBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;

class GlobalVariables extends BaseGlobalVariables
{
    public function getSite()
    {
        $siteFactory = $this->container->get('vivo_site.factory.site');

        return $siteFactory->get();
    }

    public function getSeo()
    {
        return $this->container->get('vivo_site.seo.page');
    }
}
