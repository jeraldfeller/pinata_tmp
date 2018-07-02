<?php

namespace Vivo\PageBundle\Menu;

interface ItemInterface
{
    /**
     * @return mixed|\Vivo\PageBundle\Model\MenuNodeInterface
     */
    public function getItemOwner();

    /**
     * Set href.
     *
     * @param $href
     *
     * @return $this
     */
    public function setHref($href);

    /**
     * Get href.
     *
     * @return string
     */
    public function getHref();

    /**
     * Set name.
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set itemTemplate.
     *
     * @param string $itemTemplate
     *
     * @return $this
     */
    public function setItemTemplate($itemTemplate);

    /**
     * Get itemTemplate.
     *
     * @return string
     */
    public function getItemTemplate();

    /**
     * Set raw content.
     *
     * @deprecated setRawContent() is deprecated since version 3.6 and will be removed in 4.0. Use setItemTemplate() instead.
     *
     * @param string $rawContent
     *
     * @return $this
     */
    public function setRawContent($rawContent);

    /**
     * Get raw content.
     *
     * @deprecated getRawContent() is deprecated since version 3.6 and will be removed in 4.0. Use getItemTemplate() instead.
     *
     * @return string
     */
    public function getRawContent();

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return $this
     */
    public function setActive($active);

    /**
     * Is active?
     *
     * @return bool
     */
    public function isActive();

    /**
     * Set parent.
     *
     * @param ItemInterface $parent
     *
     * @return $this
     */
    public function setParent(ItemInterface $parent = null);

    /**
     * Get parent.
     *
     * @return ItemInterface
     */
    public function getParent();

    /**
     * Add child.
     *
     * @param ItemInterface $child
     *
     * @return $this
     */
    public function addChild(ItemInterface $child);

    /**
     * Remove child.
     *
     * @param ItemInterface $child
     *
     * @return $this
     */
    public function removeChild(ItemInterface $child);

    /**
     * Get children.
     *
     * @return ItemInterface[]
     */
    public function getChildren();

    /**
     * Set attribute.
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute($name, $value);

    /**
     * Get attribute.
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function getAttribute($name, $default = null);

    /**
     * Get attributes.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Set link attribute.
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setLinkAttribute($name, $value);

    /**
     * Get link attribute.
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function getLinkAttribute($name, $default = null);

    /**
     * Get link attributes.
     *
     * @return array
     */
    public function getLinkAttributes();

    /**
     * Set option.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($name, $value);

    /**
     * Get option.
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null);
}
