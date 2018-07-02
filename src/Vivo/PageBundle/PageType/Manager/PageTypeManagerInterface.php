<?php

namespace Vivo\PageBundle\PageType\Manager;

use Vivo\PageBundle\PageType\Type\PageTypeInterface;

interface PageTypeManagerInterface
{
    /**
     * Get the default page type.
     *
     * @return null|PageTypeInterface
     */
    public function getDefault();

    /**
     * Returns a new instance of the page type.
     *
     * @param $alias
     *
     * @return PageTypeInterface|null
     */
    public function getPageTypeInstanceByAlias($alias);

    /**
     * Get all the PageTypes.
     *
     * @return \Vivo\PageBundle\PageType\Type\PageTypeInterface[]
     */
    public function getPageTypes();

    /**
     * Add a page type.
     *
     * @param \Vivo\PageBundle\PageType\Type\PageTypeInterface $pageType
     * @param $alias
     *
     * @throws \Exception
     */
    public function addPageType(PageTypeInterface $pageType, $alias);
}
