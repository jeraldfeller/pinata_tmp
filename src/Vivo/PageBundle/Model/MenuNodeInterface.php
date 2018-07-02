<?php

namespace Vivo\PageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\SiteBundle\Doctrine\FilterAware\SiteAwareInterface;
use Vivo\TreeBundle\Model\TreeInterface;

interface MenuNodeInterface extends TreeInterface, SiteAwareInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

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
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return $this
     */
    public function setRank($rank);

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank();

    /**
     * Set disabled.
     *
     * @param bool $disabled
     *
     * @return $this
     */
    public function setDisabled($disabled);

    /**
     * Is disabled?
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Set menu.
     *
     * @return $this
     */
    public function setMenu(MenuNodeInterface $menu);

    /**
     * Get menu.
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface|null
     */
    public function getMenu();

    /**
     * Set parent.
     *
     * @return $this
     */
    public function setParent(MenuNodeInterface $parent = null);

    /**
     * Get parent.
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface
     */
    public function getParent();

    /**
     * Set children.
     *
     * @param \Vivo\PageBundle\Model\MenuNodeInterface[] $children
     *
     * @return $this
     */
    public function setChildren(ArrayCollection $children);

    /**
     * Add children.
     *
     * @param \Vivo\PageBundle\Model\MenuNodeInterface $child
     *
     * @return $this
     */
    public function addChild(MenuNodeInterface $child);

    /**
     * Remove children.
     *
     * @param \Vivo\PageBundle\Model\MenuNodeInterface $child
     */
    public function removeChild(MenuNodeInterface $child);

    /**
     * Get children.
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface[]
     */
    public function getChildren();

    /**
     * Get active children.
     *
     * @return \Vivo\PageBundle\Model\MenuNodeInterface[]
     */
    public function getActiveChildren();

    /**
     * Set page.
     *
     * @param \Vivo\PageBundle\Model\PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null);

    /**
     * Get page.
     *
     * @return \Vivo\PageBundle\Model\PageInterface
     */
    public function getPage();

    /**
     * Return the RouteName for the MenuNode.
     *
     * @return null|string
     */
    public function getRouteName();

    /**
     * Return a token based on the intention.
     *
     * @param $intention
     *
     * @return string
     */
    public function getCsrfIntention($intention);

    /**
     * Get primary.
     *
     * @return bool
     */
    public function isPrimary();

    /**
     * Get the maximum menu depth
     *   - Return 0 to disable
     *   - Return null for unlimited.
     *
     * @return int|null
     */
    public function getMaxDepth();
}
