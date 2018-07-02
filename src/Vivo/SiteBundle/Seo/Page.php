<?php

namespace Vivo\SiteBundle\Seo;

use Vivo\SiteBundle\Factory\SiteFactory;

class Page implements PageInterface
{
    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @var string
     */
    protected $siteNameTitle;

    /**
     * @var array
     */
    protected $titleParts = array();

    /**
     * @var array
     */
    protected $breadcrumbs = array();

    /**
     * @var string
     */
    protected $canonicalRouteName;

    /**
     * @var array
     */
    protected $canonicalRouteParameters = array();

    /**
     * @var array
     */
    protected $meta = array(
        'name' => array(),
        'property' => array(),
    );

    /**
     * @param SiteFactory $siteFactory
     */
    public function __construct(SiteFactory $siteFactory)
    {
        $this->siteFactory = $siteFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setSiteNameTitle($siteNameTitle)
    {
        $this->siteNameTitle = $siteNameTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteNameTitle()
    {
        if (null === $this->siteNameTitle) {
            if (null !== $site = $this->getSite()) {
                $this->siteNameTitle = $this->getSite()->getName();
            }
        }

        return $this->siteNameTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitleParts(array $titleParts = array())
    {
        $this->titleParts = $titleParts;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTitlePart($titlePart)
    {
        $this->titleParts[] = $titlePart;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleParts()
    {
        return $this->titleParts;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle($reverse = true, $siteNameSeparator = ' - ', $titleSeparator = ' - ')
    {
        $title = array();
        $titleParts = $this->titleParts;

        if (count($titleParts) > 0) {
            if ($reverse) {
                $titleParts = array_reverse($titleParts);
            }

            if (false === $siteNameSeparator) {
                return implode($titleSeparator, $titleParts);
            }

            $title[] = implode($titleSeparator, $titleParts);
        } else {
            return $this->getSiteNameTitle();
        }

        $siteNameTitle = $this->getSiteNameTitle();

        if (strlen($siteNameTitle) > 0) {
            array_unshift($title, $siteNameTitle);

            if ($reverse) {
                $title = array_reverse($title);
            }
        }

        return implode($siteNameSeparator, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function addBreadcrumb($title, $routeName, array $routeParameters = array())
    {
        $this->breadcrumbs[] = array($title, $routeName, $routeParameters);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        if (!isset($this->meta['name']['google-site-verification'])) {
            $site = $this->getSite();

            if (null !== $site && (null !== $verificationCode = $site->getGoogleSiteVerificationCode())) {
                $this->addMeta('name', 'google-site-verification', $verificationCode);
            }
        }

        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function addMeta($type, $name, $content, $singular = true)
    {
        if (!isset($this->meta[$type])) {
            $this->meta[$type] = array();
        }

        if ($singular) {
            $this->meta[$type][$name] = array($content);
        } else {
            $this->meta[$type][$name][] = $content;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMeta($type, $name)
    {
        return isset($this->meta[$type][$name]) ? true : false;
    }

    /**
     * @param $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription)
    {
        $this->addMeta('name', 'description', $metaDescription);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCanonicalRouteName($canonicalRouteName)
    {
        $this->canonicalRouteName = $canonicalRouteName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalRouteName()
    {
        return $this->canonicalRouteName;
    }

    /**
     * {@inheritdoc}
     */
    public function setCanonicalRouteParameters(array $canonicalRouteParameters = array())
    {
        $this->canonicalRouteParameters = $canonicalRouteParameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalRouteParameters()
    {
        return $this->canonicalRouteParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->siteFactory->get();
    }
}
