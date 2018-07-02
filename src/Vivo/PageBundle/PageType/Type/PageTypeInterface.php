<?php

namespace Vivo\PageBundle\PageType\Type;

use Vivo\PageBundle\Model\MenuNodeInterface;
use Vivo\PageBundle\Model\PageInterface;

interface PageTypeInterface
{
    /**
     * Return true if default.
     *
     * @return bool
     */
    public function isDefault();

    /**
     * Set page.
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page);

    /**
     * Get page.
     *
     * @return PageInterface
     */
    public function getPage();

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Returns the name of this PageType.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the content blocks for this PageType.
     *
     * @return \Vivo\PageBundle\PageType\Block\ContentBlockInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getContentBlocks();

    /**
     * Return a content block by its alias if available.
     *
     * @param $alias
     *
     * @return \Vivo\PageBundle\PageType\Block\ContentBlockInterface|null
     */
    public function getContentBlockByAlias($alias);

    /**
     * Returns the asset collection blocks for this PageType.
     *
     * @return \Vivo\PageBundle\PageType\Block\AssetGroupBlockInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getAssetGroupBlocks();

    /**
     * Return a AssetGroup block by its alias if available.
     *
     * @param $alias
     *
     * @return \Vivo\PageBundle\PageType\Block\AssetGroupBlockInterface|null
     */
    public function getAssetGroupBlockByAlias($alias);

    /**
     * Return true if the page type is enabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Return true if page cannot be disabled.
     *
     * @return bool
     */
    public function isAlwaysEnabled();

    /**
     * Return true if the page type can be changed.
     *
     * @return bool
     */
    public function isPageTypeChangable();

    /**
     * Return true if this page type is unique.
     *
     * @return bool
     */
    public function isUnique();

    /**
     * Remove any data that is not required.
     *
     * @return PageInterface
     */
    public function getSanitisedPage();

    /**
     * Return the RouteCollection for a page.
     *
     * @return \Symfony\Component\Routing\RouteCollection|\Symfony\Component\Routing\Route[]
     */
    public function getRouteCollection();

    /**
     * Get the page slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Returns the route name of the page.
     *
     * @return string|null
     */
    public function getRouteName();

    /**
     * Return the controller that will return the Page Response.
     *
     * @return string
     */
    public function getController();

    /**
     * Get Form Type.
     *
     * @return string|\Symfony\Component\Form\FormInterface
     */
    public function getFormType();

    /**
     * Return validation groups for form.
     *
     * @return array
     */
    public function getValidationGroups();

    /**
     * Get the maximum menu depth for a page
     *   - Return 0 to disable
     *   - Return null for unlimited.
     *
     * @param MenuNodeInterface $menuNode
     *
     * @return int|null
     */
    public function getMaxMenuDepth(MenuNodeInterface $menuNode);
}
