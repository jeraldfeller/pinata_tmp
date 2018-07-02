<?php

namespace Vivo\SiteBundle\Model;

/**
 * SiteInterface.
 */
interface SiteInterface
{
    const STATUS_STAGING = 0;
    const STATUS_LIVE = 1;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get status name.
     *
     * @return string
     */
    public function getStatusName();

    /**
     * Return true if the site is live.
     *
     * @return bool
     */
    public function isLive();

    /**
     * Get array of statuses.
     *
     * @return array
     */
    public static function getStatuses();

    /**
     * Set mailer.
     *
     * @param string $mailer
     *
     * @return $this
     */
    public function setMailer($mailer);

    /**
     * Get mailer.
     *
     * @return string
     */
    public function getMailer();

    /**
     * Set short name.
     *
     * @param string $shortName
     *
     * @return $this
     */
    public function setShortName($shortName);

    /**
     * Get short name.
     *
     * @return string
     */
    public function getShortName();

    /**
     * Set theme.
     *
     * @param string $theme
     *
     * @return $this
     */
    public function setTheme($theme);

    /**
     * Get theme.
     *
     * @return string
     */
    public function getTheme();

    /**
     * Set googleAnalyticsId.
     *
     * @param string $googleAnalyticsId
     *
     * @return $this
     */
    public function setGoogleAnalyticsId($googleAnalyticsId);

    /**
     * Get googleAnalyticsId.
     *
     * @return string
     */
    public function getGoogleAnalyticsId();

    /**
     * Set googleSiteVerificationCode.
     *
     * @param string $googleSiteVerificationCode
     *
     * @return $this
     */
    public function setGoogleSiteVerificationCode($googleSiteVerificationCode);

    /**
     * Get googleSiteVerificationCode.
     *
     * @return string
     */
    public function getGoogleSiteVerificationCode();

    /**
     * Set googleApiServerKey.
     *
     * @param string $googleApiServerKey
     *
     * @return $this
     */
    public function setGoogleApiServerKey($googleApiServerKey);

    /**
     * Get googleApiServerKey.
     *
     * @return string
     */
    public function getGoogleApiServerKey();

    /**
     * Set googleApiBrowserKey.
     *
     * @param string $googleApiBrowserKey
     *
     * @return $this
     */
    public function setGoogleApiBrowserKey($googleApiBrowserKey);

    /**
     * Get googleApiBrowserKey.
     *
     * @return string
     */
    public function getGoogleApiBrowserKey();

    /**
     * Set senderName.
     *
     * @param string $senderName
     *
     * @return $this
     */
    public function setSenderName($senderName);

    /**
     * Get senderName.
     *
     * @return string
     */
    public function getSenderName();

    /**
     * Set senderEmail.
     *
     * @param string $senderEmail
     *
     * @return $this
     */
    public function setSenderEmail($senderEmail);

    /**
     * Get senderEmail.
     *
     * @return string
     */
    public function getSenderEmail();

    /**
     * Set senderEmail.
     *
     * @param string $senderEmail
     *
     * @return $this
     */
    public function setNotificationEmail($notificationEmail);

    /**
     * Get notificationEmail.
     *
     * @return string
     */
    public function getNotificationEmail();

    /**
     * Add domains.
     *
     * @param \Vivo\SiteBundle\Model\DomainInterface $domain
     *
     * @return $this
     */
    public function addDomain(DomainInterface $domain);

    /**
     * Remove domains.
     *
     * @param \Vivo\SiteBundle\Model\DomainInterface $domains
     */
    public function removeDomain(DomainInterface $domains);

    /**
     * Get domains.
     *
     * @return \Vivo\SiteBundle\Model\DomainInterface[]
     */
    public function getDomains();

    /**
     * Get primaryDomain.
     *
     * @return \Vivo\SiteBundle\Model\DomainInterface|null
     */
    public function getPrimaryDomain();

    /**
     * Return a token based on the intention.
     *
     * @param $intention
     *
     * @return string
     */
    public function getCsrfIntention($intention);

    /**
     * Set primary.
     *
     * @param bool $primary
     *
     * @return $this
     */
    public function setPrimary($primary);

    /**
     * Get primary.
     *
     * @return bool
     */
    public function isPrimary();

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
