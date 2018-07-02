<?php

namespace Vivo\PageBundle\Menu;

class Item implements ItemInterface
{
    /**
     * @var \Vivo\PageBundle\Model\MenuNodeInterface|mixed
     */
    protected $itemOwner;

    /**
     * @var string
     */
    protected $href;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $itemTemplate;

    /**
     * @var string
     */
    protected $rawContent;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $linkAttributes = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var ItemInterface
     */
    protected $parent;

    /**
     * @var ItemInterface[]
     */
    protected $children = [];

    /**
     * Constructor.
     *
     * @param \Vivo\PageBundle\Model\MenuNodeInterface|mixed
     */
    public function __construct($itemOwner)
    {
        $this->itemOwner = $itemOwner;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemOwner()
    {
        return $this->itemOwner;
    }

    /**
     * {@inheritdoc}
     */
    public function setHref($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setItemTemplate($itemTemplate)
    {
        $this->itemTemplate = $itemTemplate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTemplate()
    {
        return $this->itemTemplate;
    }

    /**
     * Set raw content.
     *
     * @deprecated setRawContent() is deprecated since version 3.6 and will be removed in 4.0. Use setItemTemplate() instead.
     *
     * @param string $rawContent
     *
     * @return $this
     */
    public function setRawContent($rawContent)
    {
        @trigger_error('setRawContent() is deprecated since version 3.6 and will be removed in 4.0. Use setItemTemplate() instead.', E_USER_DEPRECATED);

        $this->rawContent = $rawContent;

        return $this;
    }

    /**
     * Get raw content.
     *
     * @deprecated getRawContent() is deprecated since version 3.6 and will be removed in 4.0. Use getItemTemplate() instead.
     *
     * @return string
     */
    public function getRawContent()
    {
        @trigger_error('getRawContent() is deprecated since version 3.6 and will be removed in 4.0. Use getItemTemplate() instead.', E_USER_DEPRECATED);

        return $this->rawContent;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ItemInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(ItemInterface $child)
    {
        if (!in_array($child, $this->children, true)) {
            $child->setParent($this);
            $this->children[] = $child;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(ItemInterface $child)
    {
        if (false !== $key = array_search($child, $this->children, true)) {
            $child->setParent(null);
            unset($this->children[$key]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $attributes = $this->attributes;

        if ($this->active) {
            $attributes['class'] = trim($this->getAttribute('class', '').' active');
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkAttribute($name, $value)
    {
        $this->linkAttributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttribute($name, $default = null)
    {
        if (isset($this->linkAttributes[$name])) {
            return $this->linkAttributes[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttributes()
    {
        return array_merge($this->linkAttributes, array(
            'href' => $this->href,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name, $default = null)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return $default;
    }
}
