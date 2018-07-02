<?php

namespace Vivo\SiteBundle\Seo;

interface PageInterface
{
    /**
     * Set siteNameTitle.
     *
     * @param string $siteNameTitle
     *
     * @return $this
     */
    public function setSiteNameTitle($siteNameTitle);

    /**
     * Get siteNameTitle.
     *
     * @return string
     */
    public function getSiteNameTitle();

    /**
     * Set title parts.
     *
     * @param array $titleParts
     *
     * @return $this
     */
    public function setTitleParts(array $titleParts = array());

    /**
     * Add a title part.
     *
     * @param string $titlePart
     *
     * @return $this
     */
    public function addTitlePart($titlePart);

    /**
     * Get title parts.
     *
     * @return array
     */
    public function getTitleParts();

    /**
     * Get title.
     *
     * @param bool         $reverse           if true, reverse the title
     * @param string|false $siteNameSeparator
     * @param string       $titleSeparator
     *
     * @return string
     */
    public function getTitle($reverse = true, $siteNameSeparator = ' | ', $titleSeparator = ' - ');

    /**
     * Add breadcrumb.
     *
     * @param string $title
     * @param string $routeName
     * @param array  $routeParameters
     *
     * @return $this
     */
    public function addBreadcrumb($title, $routeName, array $routeParameters = array());

    /**
     * Get breadcrumbs.
     *
     * @return array
     */
    public function getBreadcrumbs();

    /**
     * Get meta.
     *
     * @return array
     */
    public function getMeta();

    /**
     * Add meta.
     *
     * @param string $type
     * @param string $name
     * @param string $content
     * @param bool   $singular - if true, meta will be overwritten if it already exists otherwise add duplicate tag
     *
     * @return $this
     */
    public function addMeta($type, $name, $content, $singular = true);

    /**
     * Return true if meta exists.
     *
     * @param string $type
     * @param string $name
     *
     * @return bool
     */
    public function hasMeta($type, $name);

    /**
     * Set meta description.
     *
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set canonical route name for page.
     *
     * @param string $canonicalRouteName
     *
     * @return $this
     */
    public function setCanonicalRouteName($canonicalRouteName);

    /**
     * Get route name.
     *
     * @return string
     */
    public function getCanonicalRouteName();

    /**
     * Set canonical route parameters.
     *
     * @param array $canonicalRouteParameters
     *
     * @return $this
     */
    public function setCanonicalRouteParameters(array $canonicalRouteParameters = array());

    /**
     * Get route parameters.
     *
     * @return array
     */
    public function getCanonicalRouteParameters();

    /**
     * Get site.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    public function getSite();
}
