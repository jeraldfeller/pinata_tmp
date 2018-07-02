<?php

namespace Vivo\SiteBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\PrimaryTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Site.
 */
class Site implements SiteInterface, AutoFlushCacheInterface
{
    use PrimaryTrait;
    use TimestampableTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $shortName;

    /**
     * @var string
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $senderName;

    /**
     * @var string
     */
    protected $senderEmail;

    /**
     * @var string
     */
    protected $notificationEmail;

    /**
     * @var string
     */
    protected $theme;

    /**
     * @var string
     */
    protected $googleAnalyticsId;

    /**
     * @var bool
     */
    protected $googleAdvertiserSupport;

    /**
     * @var string
     */
    protected $googleSiteVerificationCode;

    /**
     * @var string
     */
    protected $googleApiServerKey;

    /**
     * @var string
     */
    protected $googleApiBrowserKey;

    /**
     * @var \Vivo\SiteBundle\Model\DomainInterface[]|ArrayCollection
     */
    protected $domains;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->status = SiteInterface::STATUS_STAGING;
        $this->googleAdvertiserSupport = false;
        $this->domains = new ArrayCollection();
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
    public function setName($name)
    {
        $this->name = null === $name ? null : (string) $name;

        if (null === $this->shortName) {
            $this->setShortName($name);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->status = null === $status ? null : (int) $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusName()
    {
        $statuses = self::getStatuses();

        if (false !== array_key_exists($this->status, $statuses)) {
            return $statuses[$this->status];
        }

        return 'Unknown';
    }

    /**
     * {@inheritdoc}
     */
    public function isLive()
    {
        return SiteInterface::STATUS_LIVE === $this->status ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public static function getStatuses()
    {
        return array(
            SiteInterface::STATUS_STAGING => 'Staging',
            SiteInterface::STATUS_LIVE => 'Live',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setMailer($mailer)
    {
        $this->mailer = null === $mailer ? null : (string) $mailer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function setShortName($shortName)
    {
        $this->shortName = null === $shortName ? $this->name : (string) $shortName;

        if (null === $this->senderName) {
            $this->setSenderName($this->shortName);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * {@inheritdoc}
     */
    public function setTheme($theme)
    {
        $this->theme = null === $theme ? null : trim((string) $theme, '/');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set googleAnalyticsId.
     *
     * @param string $googleAnalyticsId
     *
     * @return Site
     */
    public function setGoogleAnalyticsId($googleAnalyticsId)
    {
        $this->googleAnalyticsId = null === $googleAnalyticsId ? null : (string) $googleAnalyticsId;

        return $this;
    }

    /**
     * Get googleAnalyticsId.
     *
     * @return string
     */
    public function getGoogleAnalyticsId()
    {
        return $this->googleAnalyticsId;
    }

    /**
     * Set googleAdvertiserSupport.
     *
     * @param bool $googleAdvertiserSupport
     *
     * @return Site
     */
    public function setGoogleAdvertiserSupport($googleAdvertiserSupport)
    {
        $this->googleAdvertiserSupport = (bool) $googleAdvertiserSupport;

        return $this;
    }

    /**
     * Get googleAdvertiserSupport.
     *
     * @return bool
     */
    public function getGoogleAdvertiserSupport()
    {
        return $this->googleAdvertiserSupport;
    }

    /**
     * Set googleSiteVerificationCode.
     *
     * @param string $googleSiteVerificationCode
     *
     * @return Site
     */
    public function setGoogleSiteVerificationCode($googleSiteVerificationCode)
    {
        $this->googleSiteVerificationCode = null === $googleSiteVerificationCode ? null : (string) $googleSiteVerificationCode;

        return $this;
    }

    /**
     * Get googleSiteVerificationCode.
     *
     * @return string
     */
    public function getGoogleSiteVerificationCode()
    {
        return $this->googleSiteVerificationCode;
    }

    /**
     * Set googleApiServerKey.
     *
     * @param string $googleApiServerKey
     *
     * @return Site
     */
    public function setGoogleApiServerKey($googleApiServerKey)
    {
        $this->googleApiServerKey = null === $googleApiServerKey ? null : (string) $googleApiServerKey;

        return $this;
    }

    /**
     * Get googleApiServerKey.
     *
     * @return string
     */
    public function getGoogleApiServerKey()
    {
        return $this->googleApiServerKey;
    }

    /**
     * Set googleApiBrowserKey.
     *
     * @param string $googleApiBrowserKey
     *
     * @return Site
     */
    public function setGoogleApiBrowserKey($googleApiBrowserKey)
    {
        $this->googleApiBrowserKey = null === $googleApiBrowserKey ? null : (string) $googleApiBrowserKey;

        return $this;
    }

    /**
     * Get googleApiBrowserKey.
     *
     * @return string
     */
    public function getGoogleApiBrowserKey()
    {
        return $this->googleApiBrowserKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderName($senderName)
    {
        $this->senderName = null === $senderName ? $this->shortName : (string) $senderName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = null === $senderEmail ? null : (string) $senderEmail;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotificationEmail($notificationEmail)
    {
        $this->notificationEmail = null === $notificationEmail ? null : (string) $notificationEmail;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationEmail()
    {
        return $this->notificationEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function addDomain(DomainInterface $domain)
    {
        if (!$this->domains->contains($domain)) {
            $domain->setSite($this);
            $this->domains->add($domain);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeDomain(DomainInterface $domains)
    {
        $this->domains->removeElement($domains);
    }

    /**
     * {@inheritdoc}
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryDomain()
    {
        $criteria = Criteria::create()
            ->orderBy(array('primary' => Criteria::DESC, 'id' => Criteria::ASC))
            ->setMaxResults(1);

        return $this->getDomains()->matching($criteria)->first() ?: null;
    }
}
