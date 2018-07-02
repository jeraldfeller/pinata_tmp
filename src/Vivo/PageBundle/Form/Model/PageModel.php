<?php

namespace Vivo\PageBundle\Form\Model;

use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\PageType\Type\PageTypeInterface;

/**
 * Class PageModel.
 */
class PageModel
{
    /**
     * @var \Vivo\PageBundle\PageType\Type\PageTypeInterface
     */
    protected $pageTypeInstance;

    /**
     * @var \Vivo\PageBundle\Model\PageInterface
     */
    protected $page;

    /**
     * @param PageInterface $page
     */
    public function __construct(PageInterface $page)
    {
        $this->page = $page;
        $this->pageTypeInstance = $page->getPageTypeInstance();
    }

    /**
     * @param PageTypeInterface $pageTypeInstance
     */
    public function setPageTypeInstance(PageTypeInterface $pageTypeInstance = null)
    {
        $this->pageTypeInstance = $pageTypeInstance;

        $this->page->setPageTypeInstance($pageTypeInstance);
    }

    /**
     * @return PageTypeInterface
     */
    public function getPageTypeInstance()
    {
        return $this->pageTypeInstance;
    }

    /**
     * @param PageInterface $page
     */
    public function setPage(PageInterface $page)
    {
        $page->setPageTypeInstance($this->pageTypeInstance);

        $this->page = $page;
    }

    /**
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }
}
