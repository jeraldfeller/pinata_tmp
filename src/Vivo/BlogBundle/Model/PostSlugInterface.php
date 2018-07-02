<?php

namespace Vivo\BlogBundle\Model;

use Vivo\SlugBundle\Model\SlugInterface;

/**
 * PostSlugInterface.
 */
interface PostSlugInterface extends SlugInterface
{
    /**
     * Set post.
     *
     * @param \Vivo\BlogBundle\Model\PostInterface $post
     *
     * @return $this
     */
    public function setPost(PostInterface $post);

    /**
     * Get post.
     *
     * @return \Vivo\BlogBundle\Model\PostInterface
     */
    public function getPost();
}
