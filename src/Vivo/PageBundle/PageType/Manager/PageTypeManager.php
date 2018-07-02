<?php

namespace Vivo\PageBundle\PageType\Manager;

use Vivo\PageBundle\PageType\Type\DisabledPageType;
use Vivo\PageBundle\PageType\Type\PageTypeInterface;

class PageTypeManager implements PageTypeManagerInterface
{
    /**
     * @var \Vivo\PageBundle\PageType\Type\PageTypeInterface[]
     */
    protected $pageTypes = [];

    /**
     * @var \Vivo\PageBundle\PageType\Type\PageTypeInterface[]
     */
    protected $disabledPageTypes = [];

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        foreach ($this->getPageTypes() as $pageType) {
            if ($pageType->isEnabled() && $pageType->isDefault()) {
                return $pageType;
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTypeInstanceByAlias($alias)
    {
        if ($pageType = $this->getPageTypeByAlias($alias)) {
            $pageType = clone $pageType;

            if ($pageType->isEnabled()) {
                return $pageType;
            }
        }

        return new DisabledPageType($pageType);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTypes()
    {
        return $this->pageTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function addPageType(PageTypeInterface $pageType, $alias)
    {
        if ($this->getPageTypeByAlias($alias)) {
            throw new \Exception(sprintf("'%s' is not a unique page type alias.", $alias));
        }

        if ($alias !== $pageType->getAlias()) {
            throw new \Exception(sprintf("'%s' alias must match PageType alias '%s'", $alias, $pageType->getAlias()));
        }

        if ($pageType->isEnabled()) {
            $this->pageTypes[$alias] = $pageType;
        } else {
            $this->disabledPageTypes[$alias] = $pageType;
        }
    }

    /**
     * Return the PageTypeInterface class for an alias.
     *
     * @param $alias
     *
     * @return PageTypeInterface|null
     */
    protected function getPageTypeByAlias($alias)
    {
        if (false !== array_key_exists($alias, $this->pageTypes)) {
            return $this->pageTypes[$alias];
        }

        if (false !== array_key_exists($alias, $this->disabledPageTypes)) {
            return $this->disabledPageTypes[$alias];
        }

        return;
    }
}
