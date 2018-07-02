<?php

namespace Vivo\BlogBundle\Model;

use Vivo\SlugBundle\Model\Slug as BaseSlug;

/**
 * CategorySlug.
 */
class CategorySlug extends BaseSlug implements CategorySlugInterface
{
    /**
     * @var CategoryInterface
     */
    protected $category;

    protected $a;

    /**
     * {@inheritdoc}
     */
    public function setCategory(CategoryInterface $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return $this === $this->category->getPrimarySlug() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        if (null === $this->category) {
            return;
        }

        return $this->category->getSite();
    }
}
