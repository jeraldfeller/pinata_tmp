<?php

namespace Vivo\PageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\SiteBundle\Behaviour\SiteTrait;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\PrimaryTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * MenuNode.
 */
class MenuNode implements MenuNodeInterface
{
    use SiteTrait;
    use PrimaryTrait;
    use TimestampableTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $rank;

    /**
     * @var bool
     */
    protected $disabled;

    /**
     * @var MenuNodeInterface
     */
    protected $menu;

    /**
     * @var MenuNodeInterface
     */
    protected $parent;

    /**
     * @var \Vivo\PageBundle\Model\MenuNodeInterface[]
     */
    protected $children;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rank = 9999;
        $this->disabled = false;
        $this->primary = false;
        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($alias)
    {
        $this->alias = null === $alias ? null : (string) $alias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = null === $title ? null : (string) $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setRank($rank)
    {
        $this->rank = (int) $rank;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setMenu(MenuNodeInterface $menu)
    {
        $this->menu = $menu;

        foreach ($this->getChildren() as $child) {
            $child->setMenu($menu);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(MenuNodeInterface $parent = null)
    {
        $this->parent = $parent;

        if (null === $parent) {
            foreach ($this->getChildren() as $child) {
                $child->setMenu($this);
            }
        } else {
            if (null !== $parent->getMenu()) {
                $this->setMenu($parent->getMenu());
            } else {
                $this->setMenu($parent);
            }
        }

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
    public function setChildren(ArrayCollection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(MenuNodeInterface $child)
    {
        if (!$this->children->contains($child)) {
            if ($child->getParent() && $this !== $child->getParent()) {
                $child->getParent()->removeChild($child);
            }

            $child->setParent($this);
            $this->children->add($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(MenuNodeInterface $child)
    {
        $this->children->removeElement($child);
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
    public function getActiveChildren()
    {
        $collection = new ArrayCollection();
        foreach ($this->children as $child) {
            if (!$child->isDisabled()) {
                $collection->add($child);
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(PageInterface $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        if (!$this->getParent()) {
            // If there is no parent it must be a menu
            return;
        }

        if ($this->getPage() && $this->getPage()->getPageTypeInstance()) {
            if ($routeName = $this->getPage()->getPageTypeInstance()->getRouteName()) {
                return $routeName;
            }
        }

        if (count($children = $this->getActiveChildren()) > 0) {
            foreach ($children as $child) {
                if ($child->getPage() && $child->getPage()->getPageTypeInstance() && ($routeName = $child->getPage()->getPageTypeInstance()->getRouteName())) {
                    return $routeName;
                }
            }

            foreach ($children as $child) {
                if (null !== $routeName = $child->getRouteName()) {
                    return $routeName;
                }
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxDepth()
    {
        if ($this->page) {
            return $this->page->getPageTypeInstance()->getMaxMenuDepth($this);
        }

        return;
    }

    /**
     * Fields to ignore when updating the timestamp.
     *
     * @return array
     */
    public function getIgnoredUpdateFields()
    {
        return array('rank', 'disabled');
    }

    /**
     * Get models to cascade the updated timestamp to.
     *
     * @return \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait[]
     */
    public function getCascadedTimestampableFields()
    {
        return array($this->getPage());
    }
}
