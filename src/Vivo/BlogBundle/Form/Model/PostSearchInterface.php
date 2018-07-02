<?php

namespace Vivo\BlogBundle\Form\Model;

use Vivo\BlogBundle\Model\Category;

interface PostSearchInterface
{
    /**
     * Set category.
     *
     * @param Category $category
     *
     * @return $this
     */
    public function setCategory(Category $category = null);

    /**
     * Get category.
     *
     * @return Category
     */
    public function getCategory();
}
