<?php

namespace Vivo\BlogBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Vivo\AdminBundle\Model\UserInterface;
use Vivo\SiteBundle\Behaviour\SiteTrait;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Post.
 */
class Post implements PostInterface, AutoFlushCacheInterface
{
    use TimestampableTrait;
    use SiteTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $publicationDate;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $excerpt;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var \DateTime
     */
    protected $disabledAt;

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
     * @var \Vivo\AdminBundle\Model\UserInterface
     */
    protected $owner;

    /**
     * @var \Vivo\BlogBundle\Model\PostSlugInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    protected $slugs;

    /**
     * @var PostSlugInterface
     */
    protected $primarySlug;

    /**
     * @var ArrayCollection|CategoryInterface
     */
    protected $categories;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->publicationDate = new \DateTime();
        $this->slugs = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
    public function setPublicationDate(\DateTime $publicationDate = null)
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
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
    public function setExcerpt($excerpt)
    {
        $this->excerpt = null === $excerpt ? null : (string) $excerpt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = null === $body ? null : (string) $body;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisabledAt(\DateTime $disabledAt = null)
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisabledAt()
    {
        return $this->disabledAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisabled()
    {
        if (null === $this->disabledAt) {
            return false;
        }

        return true;
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
    public function setOwner(UserInterface $owner = null, $updateOwner = false)
    {
        $this->owner = $owner;

        if ($updateOwner) {
            $this->author = $owner ? $owner->getFullName() : null;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function addSlug(PostSlugInterface $slug)
    {
        if (!$this->slugs->contains($slug)) {
            if ($slug->getPost()) {
                $slug->getPost()->removeSlug($slug);
            }

            $slug->setPost($this);

            $this->slugs[] = $slug;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSlug(PostSlugInterface $slug)
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
    public function setPrimarySlug(PostSlugInterface $primarySlug = null)
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
     * {@inheritdoc}
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
    public function addCategory(CategoryInterface $category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCategory(CategoryInterface $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCategory(CategoryInterface $category)
    {
        if (!$category->getId()) {
            return false;
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('id', $category->getId()))
            ->setMaxResults(1);

        return $this->getCategories()->matching($criteria)->first() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return !$this->isDisabled() && $this->isPublished();
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return $this->getPublicationDate() <= new \DateTime();
    }
}
