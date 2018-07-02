<?php

namespace Vivo\BlogBundle\Model;

use Vivo\SlugBundle\Model\Slug as BaseSlug;

/**
 * PostSlug.
 */
class PostSlug extends BaseSlug implements PostSlugInterface
{
    /**
     * @var PostInterface
     */
    protected $post;

    /**
     * {@inheritdoc}
     */
    public function setPost(PostInterface $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return $this === $this->post->getPrimarySlug() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        if (null === $this->post) {
            return;
        }

        return $this->post->getSite();
    }
}
