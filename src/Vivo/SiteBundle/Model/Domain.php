<?php

namespace Vivo\SiteBundle\Model;

use Vivo\SiteBundle\Util\HostUtil;
use Vivo\UtilBundle\Behaviour\Model\PrimaryTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;
use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;

/**
 * Site.
 */
class Domain implements DomainInterface, AutoFlushCacheInterface
{
    use PrimaryTrait;
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $secure;

    /**
     * @var string
     */
    protected $hasWwwSubdomain;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->secure = false;
        $this->hasWwwSubdomain = false;
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
    public function setHost($host)
    {
        $this->host = (string) HostUtil::format($host);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost($prependWww = false)
    {
        if (true === $prependWww && $this->hasWwwSubdomain()) {
            return 'www.'.$this->host;
        }

        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setSite(SiteInterface $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecure($secure)
    {
        $this->secure = (bool) $secure;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * {@inheritdoc}
     */
    public function setWwwSubdomain($hasWwwSubdomain)
    {
        $this->hasWwwSubdomain = (bool) $hasWwwSubdomain;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasWwwSubdomain()
    {
        return $this->hasWwwSubdomain;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return 'http://'.$this->getHost(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getSecureUrl()
    {
        if (!$this->isSecure()) {
            return $this->getUrl();
        }

        return 'https://'.$this->getHost(true);
    }
}
