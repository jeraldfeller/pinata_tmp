<?php

namespace Vivo\PageBundle\Model;

use Vivo\SlugBundle\Model\SlugInterface as BaseSlugInterface;

/**
 * SlugInterface.
 */
interface SlugInterface extends BaseSlugInterface
{
    /**
     * Set page.
     *
     * @param \Vivo\PageBundle\Model\PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page);

    /**
     * Get page.
     *
     * @return \Vivo\PageBundle\Model\PageInterface
     */
    public function getPage();
}
