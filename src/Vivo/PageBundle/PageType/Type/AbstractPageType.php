<?php

namespace Vivo\PageBundle\PageType\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Vivo\PageBundle\Model\AssetGroupInterface;
use Vivo\PageBundle\Model\MenuNodeInterface;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\Model\ContentInterface;
use Vivo\PageBundle\Exception\PageNotDefined;
use Vivo\PageBundle\PageType\Block\BlockInterface;
use Vivo\PageBundle\PageType\Block\ContentBlockInterface;
use Vivo\PageBundle\PageType\Block\AssetGroupBlockInterface;

abstract class AbstractPageType implements PageTypeInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var \Vivo\PageBundle\Model\PageInterface
     */
    protected $page;

    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(PageInterface $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage()
    {
        if (!$this->page) {
            throw new PageNotDefined('Page is not defined.');
        }

        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentBlocks()
    {
        return $this->getBlocks()->filter(function (BlockInterface $block) {
            return $block instanceof ContentBlockInterface;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getContentBlockByAlias($alias)
    {
        $result = $this->getContentBlocks()->filter(function (BlockInterface $block) use ($alias) {
            return $block->getAlias() === $alias;
        });

        return $result->first() ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetGroupBlocks()
    {
        return $this->getBlocks()->filter(function (BlockInterface $block) {
            return $block instanceof AssetGroupBlockInterface;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetGroupBlockByAlias($alias)
    {
        foreach ($this->getAssetGroupBlocks() as $block) {
            if ($alias === $block->getAlias()) {
                return $block;
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAlwaysEnabled()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isPageTypeChangable()
    {
        return !$this->isAlwaysEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSanitisedPage()
    {
        $page = $this->getPage();
        $page = $this->sanitiseContentBlocks($page);
        $page = $this->sanitiseAssetGroupBlocks($page);

        return $page;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $collection = new RouteCollection();

        try {
            /** @var \Vivo\PageBundle\Model\PageInterface $page */
            $page = $this->getPage();
            $route = $this->getRoute();

            if ($this->isUnique()) {
                $collection->add('vivo_page.page.page_type.'.$this->getAlias(), $route);
            }

            if ($page->getAlias()) {
                $collection->add('vivo_page.page.alias.'.$page->getAlias(), $route);
            }

            $collection->add($page->getPageTypeInstance()->getRouteName() ?: 'vivo_page.page.id.'.$this->getPage()->getId(), $route);
        } catch (PageNotDefined $e) {
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        /** @var \Vivo\PageBundle\Model\PageInterface $page */
        $page = $this->getPage();

        if ($primarySlug = $page->getPrimarySlug(true)) {
            return $primarySlug->getSlug();
        }

        return 'page/'.$page->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'vivo_page.page.id.'.$this->getPage()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return 'VivoPageBundle:Page:view';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return 'Vivo\PageBundle\Form\Type\BasePageType';
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationGroups()
    {
        $groups = [
            'Default',
            'validate_page_type_'.$this->getAlias(),
        ];

        if (null !== $this->getRouteName()) {
            $groups[] = 'validate_slug';
        }

        return $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxMenuDepth(MenuNodeInterface $menuNode)
    {
        return;
    }

    /**
     * Sanitise the content.
     *
     * @return PageInterface
     */
    protected function sanitiseContentBlocks()
    {
        $page = $this->getPage();
        $content = $page->getContent();
        $allowedAliases = [];

        // Add missing content blocks
        foreach ($this->getContentBlocks() as $block) {
            $allowedAliases[] = $block->getAlias();

            if (!$page->getContentByAlias($block->getAlias())) {
                $page->addContent($block->getModel());
            }
        }

        // Filter out any Content entities that are not required
        $content = $content->filter(function (ContentInterface $content) use ($allowedAliases) {
            return in_array($content->getAlias(), $allowedAliases, true);
        });

        // Get the Content interator
        $iterator = $content->getIterator();

        // Sort Content in the correct order
        $iterator->uasort(function (ContentInterface $first, ContentInterface $second) use ($allowedAliases) {
            if ($first === $second) {
                return 0;
            }

            return array_search($first->getAlias(), $allowedAliases) < array_search($second->getAlias(), $allowedAliases) ? -1 : 1;
        });

        $page->setContent(new ArrayCollection(iterator_to_array($iterator)));

        return $page;
    }

    /**
     * Sanitise the AssetGroups.
     *
     * @return PageInterface
     */
    protected function sanitiseAssetGroupBlocks()
    {
        $page = $this->getPage();
        $assetGroups = $page->getAssetGroups();
        $allowedAliases = [];

        // Add missing AssetGroup blocks
        foreach ($this->getAssetGroupBlocks() as $block) {
            $allowedAliases[] = $block->getAlias();

            if (!$page->getAssetGroupByAlias($block->getAlias())) {
                $page->addAssetGroup($block->getModel());
            }
        }

        // Filter out any AssetGroups that are not required
        $assetGroups = $assetGroups->filter(function (AssetGroupInterface $assetGroup) use ($allowedAliases) {
            return in_array($assetGroup->getAlias(), $allowedAliases, true);
        });

        // Get the ImageCollection interator
        $iterator = $assetGroups->getIterator();

        // Sort collection in the correct order
        $iterator->uasort(function (AssetGroupInterface $first, AssetGroupInterface $second) use ($allowedAliases) {
            if ($first === $second) {
                return 0;
            }

            return array_search($first->getAlias(), $allowedAliases) < array_search($second->getAlias(), $allowedAliases) ? -1 : 1;
        });

        $page->setAssetGroups(new ArrayCollection(iterator_to_array($iterator)));

        return $page;
    }

    /**
     * Get the Route for the Page.
     *
     * @param array  $defaults
     * @param string $appendToPath
     * @param bool   $secureRoute
     * @param array  $requirements
     *
     * @return Route
     */
    protected function getRoute(array $defaults = array(), $appendToPath = null, $secureRoute = false, array $requirements = array())
    {
        $schemes = array();
        $routeParameters = array();
        $overrideParameters = array();

        try {
            $page = $this->getPage();
            $slug = $this->getSlug();

            if ($page->isDisabled()) {
                $overrideParameters['_controller'] = 'VivoPageBundle:Page:unavailable';
            } else {
                $routeParameters['_controller'] = $this->getController();
            }

            $routeParameters['cmsPage'] = $page;

            if ($secureRoute && ($page && $page->getSite()->getPrimaryDomain()->isSecure())) {
                $schemes = array('https');
                unset($requirements['_requires_channel']);
            } else {
                /*
                 * Let Vivo\SiteBundle\EventListener\RequiresChannelListener handle the redirects
                 * We don't want to use the $schemes otherwise all urls will be rewritten without
                 * https and we may want to have a site where everything is on https
                 */
                $requirements['_requires_channel'] = 'http';
            }
        } catch (PageNotDefined $e) {
            $page = null;
            $slug = 'unavailable/'.$this->getAlias();
            $overrideParameters['_controller'] = 'VivoPageBundle:Page:unavailable';
        }

        $path = null === $appendToPath ? $slug : rtrim(rtrim($slug, '/').'/'.ltrim($appendToPath, '/'), '/');
        $parameters = array_merge($routeParameters, $defaults, $overrideParameters);

        return new Route($path, $parameters, $requirements, array(), '', $schemes, array('GET'));
    }

    /**
     * Returns the content blocks for this PageType.
     *
     * @return \Vivo\PageBundle\PageType\Block\BlockInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected function getBlocks()
    {
        return new ArrayCollection();
    }
}
