<?php

namespace App\BlogBundle\Entity;

use Vivo\AssetBundle\Model\Asset as BaseAsset;
use Doctrine\ORM\Mapping as ORM;

/**
 * PostImage.
 *
 * @ORM\Entity
 */
class PostImage extends BaseAsset
{
    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $post;

    /**
     * {@inheritdoc}
     */
    public function setPost(Post $post = null)
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
    public function setRank($rank)
    {
        parent::setRank($rank);

        return $this;
    }
}
