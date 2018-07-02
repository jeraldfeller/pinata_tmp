<?php

namespace Vivo\SlugBundle\Model;

/**
 * SlugInterface.
 */
interface SlugInterface
{
    const TEMP_SLUG_PREFIX = '_slug_301_';

    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug);

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Return true if slug is in use.
     *
     * @return bool
     */
    public function isPrimary();

    /**
     * Get site
     * This site will be used to determine if the
     * slug is unique in \Vivo\SlugBundle\Util\SlugGeneratorInterface.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface|null
     */
    public function getSite();
}
