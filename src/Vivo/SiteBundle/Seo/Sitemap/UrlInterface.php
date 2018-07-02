<?php

namespace Vivo\SiteBundle\Seo\Sitemap;

interface UrlInterface
{
    const CHANGE_FREQUENCY_ALWAYS = 'always';
    const CHANGE_FREQUENCY_HOURLY = 'hourly';
    const CHANGE_FREQUENCY_DAILY = 'daily';
    const CHANGE_FREQUENCY_WEEKLY = 'weekly';
    const CHANGE_FREQUENCY_MONTHLY = 'monthly';
    const CHANGE_FREQUENCY_YEARLY = 'yearly';
    const CHANGE_FREQUENCY_NEVER = 'never';

    /**
     * Get location.
     *
     * @param string $location
     *
     * @return $this
     */
    public function setLocation($location);

    /**
     * Set location.
     *
     * @return string|null
     */
    public function getLocation();

    /**
     * Get lastModified.
     *
     * @param \DateTime $lastModified
     *
     * @return $this
     */
    public function setLastModified(\DateTime $lastModified = null);

    /**
     * Set lastModified.
     *
     * @return string|null
     */
    public function getLastModified();

    /**
     * set changeFrequency.
     *
     * @param string $changeFrequency
     *
     * @return $this
     */
    public function setChangeFrequency($changeFrequency);

    /**
     * Get changeFrequency.
     *
     * @return string|null
     */
    public function getChangeFrequency();

    /**
     * @param float $priority
     *
     * @return $this
     */
    public function setPriority($priority);

    /**
     * @return float|null
     */
    public function getPriority();
}
