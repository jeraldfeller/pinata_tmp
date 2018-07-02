<?php

namespace Vivo\SiteBundle\Util;

use Vivo\SiteBundle\Model\SiteInterface;

interface SiteTemplateFinderInterface
{
    /**
     * Renders a template.
     *
     * @param SiteInterface $site
     * @param string        $name    The template name
     * @param array         $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     *
     * @throws \Twig_Error_Loader  When the template cannot be found
     * @throws \Twig_Error_Syntax  When an error occurred during compilation
     * @throws \Twig_Error_Runtime When an error occurred during rendering
     */
    public function render(SiteInterface $site, $name, array $context = array());

    /**
     * Loads a template by name.
     *
     * @param SiteInterface $site
     * @param string        $name  The template name
     * @param int           $index The index if it is an embedded template
     *
     * @return \Twig_TemplateInterface A template instance representing the given template name
     *
     * @throws \Twig_Error_Loader When the template cannot be found
     * @throws \Twig_Error_Syntax When an error occurred during compilation
     */
    public function loadTemplate(SiteInterface $site, $name, $index = null);
}
