<?php

namespace Vivo\PageBundle\PageType\Type;

use Vivo\PageBundle\Model\PageInterface;

class DisabledPageType extends AbstractPageType
{
    /**
     * @var PageTypeInterface|null
     */
    protected $disabledPageType;

    public function __construct(PageTypeInterface $disabledPageType = null)
    {
        $this->disabledPageType = $disabledPageType;
    }

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
        if ($this->disabledPageType) {
            $this->disabledPageType->setPage($page);
        }

        $this->page = $page;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        if ($this->disabledPageType) {
            return $this->disabledPageType->getAlias();
        } elseif ($this->page) {
            return $this->page->getPageType();
        }

        return 'vivo_page_disabled';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->disabledPageType ? $this->disabledPageType->getName().' (Disabled)' : 'Unknown';
    }

    /**
     * {@inheritdoc}
     */
    public function getContentBlocks()
    {
        return $this->disabledPageType ? $this->disabledPageType->getContentBlocks() : parent::getContentBlocks();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetGroupBlocks()
    {
        return $this->disabledPageType ? $this->disabledPageType->getAssetGroupBlocks() : parent::getAssetGroupBlocks();
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return false;
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
        return $this->disabledPageType ? $this->disabledPageType->isPageTypeChangable() : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSanitisedPage()
    {
        return $this->disabledPageType ? $this->disabledPageType->getSanitisedPage() : $this->getPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $routeCollection = $this->disabledPageType ? $this->disabledPageType->getRouteCollection() : parent::getRouteCollection();

        foreach ($routeCollection as $route) {
            $route->setDefault('_controller', 'VivoPageBundle:Page:unavailable');
        }

        return $routeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->disabledPageType ? $this->disabledPageType->getSlug() : parent::getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return $this->disabledPageType ? $this->disabledPageType->getRouteName() : parent::getRouteName();
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->disabledPageType ? $this->disabledPageType->getController() : parent::getController();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return $this->disabledPageType ? $this->disabledPageType->getFormType() : parent::getFormType();
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationGroups()
    {
        return $this->disabledPageType ? $this->disabledPageType->getValidationGroups() : parent::getValidationGroups();
    }
}
