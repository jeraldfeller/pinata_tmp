<?php

namespace Vivo\PageBundle\Model;

use Doctrine\Common\Collections\Collection;
use Vivo\PageBundle\PageType\Type\PageTypeInterface;
use Vivo\SiteBundle\Doctrine\FilterAware\SiteAwareInterface;

interface PageInterface extends SiteAwareInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get type.
     *
     * @return string
     */
    public function getPageType();

    /**
     * Set PageTypeInstance.
     *
     * @param PageTypeInterface $pageType
     *
     * @return $this
     */
    public function setPageTypeInstance(PageTypeInterface $pageType);

    /**
     * Returns the PageTypeInterface instance.
     *
     * @return \Vivo\PageBundle\PageType\Type\PageTypeInterface
     */
    public function getPageTypeInstance();

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias);

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Set pageTitle.
     *
     * @param string $pageTitle
     *
     * @return $this
     */
    public function setPageTitle($pageTitle);

    /**
     * Get pageTitle.
     *
     * @return string
     */
    public function getPageTitle();

    /**
     * Set disabledAt.
     *
     * @param \DateTime $disabledAt
     *
     * @return $this
     */
    public function setDisabledAt(\DateTime $disabledAt = null);

    /**
     * Get disabledAt.
     *
     * @return \DateTime
     */
    public function getDisabledAt();

    /**
     * Is disabled?
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Set metaTitle.
     *
     * @param string $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Get metaTitle.
     *
     * @return string
     */
    public function getMetaTitle();

    /**
     * Set metaDescription.
     *
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get metaDescription.
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set socialTitle.
     *
     * @param string $socialTitle
     *
     * @return $this
     */
    public function setSocialTitle($socialTitle);

    /**
     * Get socialTitle.
     *
     * @return string
     */
    public function getSocialTitle();

    /**
     * Set socialDescription.
     *
     * @param string $socialDescription
     *
     * @return $this
     */
    public function setSocialDescription($socialDescription);

    /**
     * Get socialDescription.
     *
     * @return string
     */
    public function getSocialDescription();

    /**
     * Set robotsNoIndex.
     *
     * @param bool $robotsNoIndex
     *
     * @return $this
     */
    public function setRobotsNoIndex($robotsNoIndex);

    /**
     * Has robotsNoIndex?
     *
     * @return bool
     */
    public function hasRobotsNoIndex();

    /**
     * Set robotsNoFollow.
     *
     * @param bool $robotsNoFollow
     *
     * @return $this
     */
    public function setRobotsNoFollow($robotsNoFollow);

    /**
     * Has robotsNoFollow?
     *
     * @return bool
     */
    public function hasRobotsNoFollow();

    /**
     * Set excludedFromSitemap.
     *
     * @param bool $excludedFromSitemap
     *
     * @return $this
     */
    public function setExcludedFromSitemap($excludedFromSitemap);

    /**
     * Is excludedFromSitemap?
     *
     * @return bool
     */
    public function isExcludedFromSitemap();

    /**
     * Set the primary slug.
     *
     * @param \Vivo\PageBundle\Model\SlugInterface $primarySlug
     *
     * @return $this
     */
    public function setPrimarySlug(SlugInterface $primarySlug);

    /**
     * Get the primary slug.
     *
     * @param bool $fallBack
     *
     * @return \Vivo\PageBundle\Model\SlugInterface
     */
    public function getPrimarySlug($fallBack = false);

    /**
     * Add slug.
     *
     * @param \Vivo\PageBundle\Model\SlugInterface $slug
     *
     * @return $this
     */
    public function addSlug(SlugInterface $slug);

    /**
     * Remove slug.
     *
     * @param \Vivo\PageBundle\Model\SlugInterface $slug
     */
    public function removeSlug(SlugInterface $slug);

    /**
     * Get content.
     *
     * @return \Vivo\PageBundle\Model\SlugInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getSlugs();

    /**
     * Add content.
     *
     * @param \Vivo\PageBundle\Model\ContentInterface $content
     *
     * @return $this
     */
    public function addContent(\Vivo\PageBundle\Model\ContentInterface $content);

    /**
     * Remove content.
     *
     * @param \Vivo\PageBundle\Model\ContentInterface $content
     */
    public function removeContent(ContentInterface $content);

    /**
     * Set content.
     *
     * @param \Vivo\PageBundle\Model\ContentInterface[]|\Doctrine\Common\Collections\ArrayCollection $content
     *
     * @return $this
     */
    public function setContent(Collection $contents);

    /**
     * Get content.
     *
     * @return \Vivo\PageBundle\Model\ContentInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getContent();

    /**
     * Return the first content matching the alias.
     *
     * @param $alias
     *
     * @return \Vivo\PageBundle\Model\ContentInterface|null
     */
    public function getContentByAlias($alias);

    /**
     * Add asset collection.
     *
     * @param \Vivo\PageBundle\Model\AssetGroupInterface $assetGroup
     *
     * @return $this
     */
    public function addAssetGroup(AssetGroupInterface $assetGroup);

    /**
     * Remove asset collection.
     *
     * @param \Vivo\PageBundle\Model\AssetGroupInterface $assetGroup
     */
    public function removeAssetGroup(AssetGroupInterface $assetGroup);

    /**
     * Set asset collections.
     *
     * @param \Vivo\PageBundle\Model\AssetGroupInterface[]|\Doctrine\Common\Collections\ArrayCollection $assetGroups
     *
     * @return $this
     */
    public function setAssetGroups(Collection $assetGroups);

    /**
     * Get asset collections.
     *
     * @return \Vivo\PageBundle\Model\AssetGroupInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getAssetGroups();

    /**
     * Return the first asset collection matching the alias.
     *
     * @param $alias
     *
     * @return \Vivo\PageBundle\Model\AssetGroupInterface|null
     */
    public function getAssetGroupByAlias($alias);

    /**
     * Set menuNodes.
     *
     * @param \Vivo\PageBundle\Model\MenuNodeInterface[]|\Doctrine\Common\Collections\ArrayCollection $menuNodes
     *
     * @return $this
     */
    public function setMenuNodes(Collection $menuNodes);

    /**
     * Add menuNode.
     *
     * @param \Vivo\PageBundle\Model\MenuNodeInterface $menuNode
     *
     * @return $this
     */
    public function addMenuNode(MenuNodeInterface $menuNode);

    /**
     * Remove menuNodes.
     *
     * @param \Vivo\PageBundle\Model\MenuNodeInterface $menuNode
     */
    public function removeMenuNode(MenuNodeInterface $menuNode);

    /**
     * Get menuNodes.
     *
     * @param bool $includeDisabled
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getMenuNodes($includeDisabled = true);

    /**
     * Returns createdAt value.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Return a hash based on the intention.
     *
     * @param $intention
     *
     * @return string
     */
    public function getCsrfIntention($intention);
}
