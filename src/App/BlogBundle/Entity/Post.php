<?php

namespace App\BlogBundle\Entity;

use App\CoreBundle\Entity\PromoBlockGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vivo\BlogBundle\Model\Post as BasePost;

/**
 * Post.
 *
 * @ORM\Table(name="vivo_blog_post")
 * @ORM\Entity(repositoryClass="App\BlogBundle\Repository\PostRepository")
 */
class Post extends BasePost
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $videoIcon;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $featured;

    /**
     * @var PostImage
     *
     * @ORM\OneToOne(targetEntity="PostImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $image;

    /**
     * @var PostImage
     *
     * @ORM\OneToOne(targetEntity="PostImage", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $contentImage;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text", nullable=true)
     */
    protected $introduction;

    /**
     * @var PromoBlockGroup
     *
     * @ORM\ManyToOne(targetEntity="App\CoreBundle\Entity\PromoBlockGroup")
     * @ORM\JoinColumn(name="promo_group_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $promoGroup;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->images = new ArrayCollection();
    }

    /**
     * Set contentImage.
     *
     * @param \App\BlogBundle\Entity\PostImage $contentImage
     */
    public function setContentImage(\App\BlogBundle\Entity\PostImage $contentImage = null)
    {
        $this->contentImage = $contentImage;

        if ($contentImage && $contentImage->getPost() !== $this) {
            $contentImage->setPost($this);
        }

        return $this;
    }

    /**
     * @return \App\BlogBundle\Entity\PostImage
     */
    public function getContentImage()
    {
        return $this->contentImage;
    }

    /**
     * Set image.
     *
     * @param \App\BlogBundle\Entity\PostImage $image
     */
    public function setImage(\App\BlogBundle\Entity\PostImage $image = null)
    {
        $this->image = $image;

        if ($image && $image->getPost() !== $this) {
            $image->setPost($this);
        }

        return $this;
    }

    /**
     * @return \App\BlogBundle\Entity\PostImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return true if entity should be indexed in elasticsearch.
     *
     * @return bool
     */
    public function isSearchIndexable()
    {
        return $this->isActive() ? true : false;
    }

    /**
     * Set videoIcon.
     *
     * @param bool $videoIcon
     */
    public function setVideoIcon($videoIcon)
    {
        $this->videoIcon = $videoIcon;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVideoIcon()
    {
        return $this->videoIcon;
    }

    /**
     * Set featured.
     *
     * @param bool $featured
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set introduction.
     *
     * @param string $introduction
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set promoGroup.
     *
     * @param PromoBlockGroup $promoGroup
     *
     * @return $this
     */
    public function setPromoGroup(PromoBlockGroup $promoGroup = null)
    {
        $this->promoGroup = $promoGroup;

        return $this;
    }

    /**
     * Get promoGroup.
     *
     * @return PromoBlockGroup
     */
    public function getPromoGroup()
    {
        return $this->promoGroup;
    }
}
