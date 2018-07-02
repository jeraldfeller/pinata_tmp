<?php

namespace Vivo\SiteBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Vivo\SiteBundle\Seo\PageInterface;

class SeoExtension extends \Twig_Extension
{
    /**
     * @var \Vivo\SiteBundle\Seo\PageInterface
     */
    protected $seoPage;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @param PageInterface   $seoPage
     * @param RouterInterface $router
     */
    public function __construct(PageInterface $seoPage, RouterInterface $router)
    {
        $this->seoPage = $seoPage;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('vivo_site_seo_title', array($this, 'getTitle'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('vivo_site_seo_meta', array($this, 'getMeta'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('vivo_site_seo_canonical_link', array($this, 'getCanonicalLink'), array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_site_seo';
    }

    /**
     * @param bool $reverse
     *
     * @return string
     */
    public function getTitle($reverse = true, $siteNameSeparator = ' - ', $titleSeparator = ' - ')
    {
        return sprintf('<title>%s</title>', $this->normalize($this->seoPage->getTitle($reverse, $siteNameSeparator, $titleSeparator)));
    }

    /**
     * @return string
     */
    public function getMeta()
    {
        $metaHtml = '';

        foreach ($this->seoPage->getMeta() as $type => $type_meta) {
            foreach ((array) $type_meta as $name => $metas) {
                foreach ($metas as $meta) {
                    if ($meta) {
                        $metaHtml .= sprintf('<meta %s="%s" content="%s" />',
                            $type,
                            $this->normalize($name),
                            $this->normalize($meta)
                        );
                    }
                }
            }
        }

        return $metaHtml;
    }

    /**
     * @return string
     */
    public function getCanonicalLink()
    {
        if ($this->seoPage->getCanonicalRouteName()) {
            /*
             * Always use the absolute path of the current Request.
             * If a specific domain is required for the canonical, the page
             * should be redirected before this is printed.
             */
            $link = $this->router->generate($this->seoPage->getCanonicalRouteName(), $this->seoPage->getCanonicalRouteParameters(), UrlGeneratorInterface::ABSOLUTE_URL);

            return sprintf('<link rel="canonical" href="%s"/>', $link);
        }

        return '';
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function normalize($string)
    {
        return htmlentities(strip_tags($string), ENT_COMPAT, 'utf-8');
    }
}
