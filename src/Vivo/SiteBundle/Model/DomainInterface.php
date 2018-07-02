<?php

namespace Vivo\SiteBundle\Model;

/**
 * DomainInterface.
 */
interface DomainInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set host.
     *
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host);

    /**
     * Get host.
     *
     * @param bool $prependWww prepend www. if it is available
     *
     * @return string
     */
    public function getHost($prependWww = false);

    /**
     * Set site.
     *
     * @param \Vivo\SiteBundle\Model\SiteInterface $site
     *
     * @return $this
     */
    public function setSite(SiteInterface $site = null);

    /**
     * Get site.
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    public function getSite();

    /**
     * Set isPrimary.
     *
     * @param bool $isPrimary
     *
     * @return $this
     */
    public function setPrimary($isPrimary);

    /**
     * Get isPrimary.
     *
     * @return bool
     */
    public function isPrimary();

    /**
     * Set secure.
     *
     * @param bool $secure
     *
     * @return $this
     */
    public function setSecure($secure);

    /**
     * Is domain secure?
     *
     * @return bool
     */
    public function isSecure();

    /**
     * Set hasWwwSubdomain.
     *
     * @param bool $hasWwwSubdomain
     *
     * @return $this
     */
    public function setWwwSubdomain($hasWwwSubdomain);

    /**
     * Get hasWwwSubdomain.
     *
     * @return bool
     */
    public function hasWwwSubdomain();

    /**
     * Get Url Scheme (eg https, http).
     *
     * @return string
     */
    public function getScheme();

    /**
     * Return the full url.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Return the secure url (if supported).
     *
     * @return string
     */
    public function getSecureUrl();
}
