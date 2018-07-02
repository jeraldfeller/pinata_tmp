<?php

namespace Vivo\BlogBundle\Model;

use Vivo\SlugBundle\Model\SlugInterface;

/**
 * CategorySlugInterface.
 */
interface CategorySlugInterface extends SlugInterface
{
    /**
     * Set category.
     *
     * @param \Vivo\BlogBundle\Model\CategoryInterface $category
     *
     * @return $this
     */
    public function setCategory(CategoryInterface $category);

    /**
     * Get category.
     *
     * @return \Vivo\BlogBundle\Model\CategoryInterface
     */
    public function getCategory();
}
