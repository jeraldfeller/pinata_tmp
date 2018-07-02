<?php

namespace Vivo\BlogBundle\Model;

use Vivo\SiteBundle\Doctrine\FilterAware\SiteAwareInterface;
use Vivo\SiteBundle\Model\SiteInterface;

/**
 * CategoryInterface.
 */
interface CategoryInterface extends SiteAwareInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

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
     * Set rank.
     *
     * @param $rank
     *
     * @return $this
     */
    public function setRank($rank);

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank();

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
     * @param \Vivo\BlogBundle\Model\CategorySlugInterface $primarySlug
     *
     * @return $this
     */
    public function setPrimarySlug(CategorySlugInterface $primarySlug);

    /**
     * Get the primary slug.
     *
     * @return \Vivo\BlogBundle\Model\CategorySlugInterface
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
     * @param \Vivo\BlogBundle\Model\CategorySlugInterface $slug
     *
     * @return $this
     */
    public function addSlug(CategorySlugInterface $slug);

    /**
     * Remove slug.
     *
     * @param \Vivo\BlogBundle\Model\CategorySlugInterface $slug
     */
    public function removeSlug(CategorySlugInterface $slug);

    /**
     * Get slugs.
     *
     * @return \Vivo\BlogBundle\Model\CategorySlugInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getSlugs();

    /**
     * Add post.
     *
     * @param PostInterface $post
     *
     * @return $this
     */
    public function addPost(PostInterface $post);

    /**
     * Get posts.
     *
     * @return \Vivo\BlogBundle\Model\PostInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getPosts();

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
     * @return \Vivo\BlogBundle\Model\CategoryInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getActivePosts();
}
