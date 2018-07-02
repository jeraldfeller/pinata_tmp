<?php

namespace Vivo\BlogBundle\Model;

use Vivo\AdminBundle\Model\UserInterface;
use Vivo\SiteBundle\Doctrine\FilterAware\SiteAwareInterface;
use Vivo\SiteBundle\Model\SiteInterface;

/**
 * PostInterface.
 */
interface PostInterface extends SiteAwareInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set publicationDate.
     *
     * @param \DateTime $publicationDate
     *
     * @return $this
     */
    public function setPublicationDate(\DateTime $publicationDate = null);

    /**
     * Get publicationDate.
     *
     * @return \DateTime
     */
    public function getPublicationDate();

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set excerpt.
     *
     * @param string $excerpt
     *
     * @return $this
     */
    public function setExcerpt($excerpt);

    /**
     * Get excerpt.
     *
     * @return string
     */
    public function getExcerpt();

    /**
     * Set body.
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body);

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody();

    /**
     * Set author.
     *
     * @param string $author
     *
     * @return $this
     */
    public function setAuthor($author);

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor();

    /**
     * Set disabledAt.
     *
     * @param \DateTime $disabledAt
     *
     * @return $this
     */
    public function setDisabledAt(\DateTime $disabledAt = null);

    /**
     * Get disabledAt.
     *
     * @return \DateTime
     */
    public function getDisabledAt();

    /**
     * Is disabled?
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Set metaTitle.
     *
     * @param string $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Get metaTitle.
     *
     * @return string
     */
    public function getMetaTitle();

    /**
     * Set metaDescription.
     *
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get metaDescription.
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set socialTitle.
     *
     * @param string $socialTitle
     *
     * @return $this
     */
    public function setSocialTitle($socialTitle);

    /**
     * Get socialTitle.
     *
     * @return string
     */
    public function getSocialTitle();

    /**
     * Set socialDescription.
     *
     * @param string $socialDescription
     *
     * @return $this
     */
    public function setSocialDescription($socialDescription);

    /**
     * Get socialDescription.
     *
     * @return string
     */
    public function getSocialDescription();

    /**
     * Set robotsNoIndex.
     *
     * @param bool $robotsNoIndex
     *
     * @return $this
     */
    public function setRobotsNoIndex($robotsNoIndex);

    /**
     * Has robotsNoIndex?
     *
     * @return bool
     */
    public function hasRobotsNoIndex();

    /**
     * Set robotsNoFollow.
     *
     * @param bool $robotsNoFollow
     *
     * @return $this
     */
    public function setRobotsNoFollow($robotsNoFollow);

    /**
     * Has robotsNoFollow?
     *
     * @return bool
     */
    public function hasRobotsNoFollow();

    /**
     * Set excludedFromSitemap.
     *
     * @param bool $excludedFromSitemap
     *
     * @return $this
     */
    public function setExcludedFromSitemap($excludedFromSitemap);

    /**
     * Is excludedFromSitemap?
     *
     * @return bool
     */
    public function isExcludedFromSitemap();

    /**
     * Set owner.
     *
     * @param UserInterface $owner
     * @param bool          $updateAuthor
     *
     * @return $this
     */
    public function setOwner(UserInterface $owner, $updateAuthor = false);

    /**
     * Get owner.
     *
     * @return UserInterface
     */
    public function getOwner();

    /**
     * Set site.
     *
     * @param \Vivo\SiteBundle\Model\SiteInterface $site
     *
     * @return $this
     */
    public function setSite(SiteInterface $site);

    /**
     * Get site.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    public function getSite();

    /**
     * Set the primary slug.
     *
     * @param \Vivo\BlogBundle\Model\PostSlugInterface $primarySlug
     *
     * @return $this
     */
    public function setPrimarySlug(PostSlugInterface $primarySlug);

    /**
     * Get the primary slug.
     *
     * @return \Vivo\BlogBundle\Model\PostSlugInterface
     */
    public function getPrimarySlug();

    /**
     * Get the primary slug string.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Add slug.
     *
     * @param \Vivo\BlogBundle\Model\PostSlugInterface $slug
     *
     * @return $this
     */
    public function addSlug(PostSlugInterface $slug);

    /**
     * Remove slug.
     *
     * @param \Vivo\BlogBundle\Model\PostSlugInterface $slug
     */
    public function removeSlug(PostSlugInterface $slug);

    /**
     * Get content.
     *
     * @return \Vivo\BlogBundle\Model\PostSlugInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getSlugs();

    /**
     * Add category.
     *
     * @param \Vivo\BlogBundle\Model\CategoryInterface $category
     *
     * @return $this
     */
    public function addCategory(CategoryInterface $category);

    /**
     * Remove category.
     *
     * @param \Vivo\BlogBundle\Model\CategoryInterface $category
     */
    public function removeCategory(CategoryInterface $category);

    /**
     * Get categories.
     *
     * @return \Vivo\BlogBundle\Model\CategoryInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getCategories();

    /**
     * @param CategoryInterface $category
     *
     * @return bool
     */
    public function hasCategory(CategoryInterface $category);

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Return a hash based on the intention.
     *
     * @param $intention
     *
     * @return string
     */
    public function getCsrfIntention($intention);

    /**
     * Return true if the post is active.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Return true if the post is published.
     *
     * @return bool
     */
    public function isPublished();
}
