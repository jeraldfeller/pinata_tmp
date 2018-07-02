<?php

namespace Vivo\PageBundle\Model;

use Vivo\SlugBundle\Model\Slug as BaseSlug;

/**
 * Slug.
 */
class Slug extends BaseSlug implements SlugInterface
{
    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * {@inheritdoc}
     */
    public function setPage(PageInterface $page)
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
    public function isPrimary()
    {
        return $this === $this->page->getPrimarySlug() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        if (null === $this->page) {
            return;
        }

        return $this->page->getSite();
    }
}
