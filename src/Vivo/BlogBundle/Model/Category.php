<?php

namespace Vivo\BlogBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Vivo\SiteBundle\Behaviour\SiteTrait;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Category.
 */
class Category implements CategoryInterface, AutoFlushCacheInterface
{
    use TimestampableTrait;
    use SiteTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $rank;

    /**
     * @var string
     */
    protected $metaTitle;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * @var string
     */
    protected $socialTitle;

    /**
     * @var string
     */
    protected $socialDescription;

    /**
     * @var bool
     */
    protected $robotsNoIndex = false;

    /**
     * @var bool
     */
    protected $robotsNoFollow = false;

    /**
     * @var bool
     */
    protected $excludedFromSitemap = false;

    /**
     * @var \Vivo\BlogBundle\Model\CategorySlugInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected $slugs;

    /**
     * @var CategorySlugInterface
     */
    protected $primarySlug;

    /**
     * @var PostInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected $posts;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rank = 9999;
        $this->slugs = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = null === $title ? null : (string) $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setRank($rank)
    {
        $this->rank = (int) $rank;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialTitle($socialTitle)
    {
        $this->socialTitle = $socialTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialTitle()
    {
        return $this->socialTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialDescription($socialDescription)
    {
        $this->socialDescription = $socialDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialDescription()
    {
        return $this->socialDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setRobotsNoIndex($robotsNoIndex)
    {
        $this->robotsNoIndex = (bool) $robotsNoIndex;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRobotsNoIndex()
    {
        return $this->robotsNoIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function setRobotsNoFollow($robotsNoFollow)
    {
        $this->robotsNoFollow = (bool) $robotsNoFollow;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRobotsNoFollow()
    {
        return $this->robotsNoFollow;
    }

    /**
     * {@inheritdoc}
     */
    public function setExcludedFromSitemap($excludedFromSitemap)
    {
        $this->excludedFromSitemap = (bool) $excludedFromSitemap;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isExcludedFromSitemap()
    {
        return $this->excludedFromSitemap;
    }

    /**
     * {@inheritdoc}
     */
    public function addPost(PostInterface $post)
    {
        if (!$this->posts->contains($post)) {
            $post->addCategory($this);
            $this->posts[] = $post;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * {@inheritdoc}
     */
    public function addSlug(CategorySlugInterface $slug)
    {
        if (!$this->slugs->contains($slug)) {
            if ($slug->getCategory()) {
                $slug->getCategory()->removeSlug($slug);
            }

            $slug->setCategory($this);

            $this->slugs[] = $slug;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSlug(CategorySlugInterface $slug)
    {
        $this->slugs->removeElement($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugs()
    {
        return $this->slugs;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrimarySlug(CategorySlugInterface $primarySlug = null)
    {
        if (null !== $primarySlug) {
            $this->addSlug($primarySlug);
        }

        $this->primarySlug = $primarySlug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimarySlug()
    {
        if (null === $this->primarySlug) {
            $criteria = Criteria::create()
                ->orderBy(array('id' => Criteria::DESC))
                ->setMaxResults(1);

            return $this->getSlugs()->matching($criteria)->first() ?: null;
        }

        return $this->primarySlug;
    }

    /**
     * @return null|string
     */
    public function getSlug()
    {
        if (null !== $slug = $this->getPrimarySlug()) {
            return $slug->getSlug();
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePosts()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('disabledAt', null))
            ->andWhere(Criteria::expr()->lte('publicationDate', new \DateTime('now')));

        return $this->posts->matching($criteria);
    }

    /**
     * Fields to ignore when updating the timestamp.
     *
     * @return array
     */
    public function getIgnoredUpdateFields()
    {
        return array('rank');
    }
}
