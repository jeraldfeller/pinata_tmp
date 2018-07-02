<?php

namespace Vivo\PageBundle\Seo;

use Vivo\PageBundle\Model\PageInterface;

class ActivePage
{
    /**
     * @var \Vivo\PageBundle\Model\PageInterface
     */
    private $page;

    /**
     * @param PageInterface $page
     */
    public function setPage(PageInterface $page = null)
    {
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
