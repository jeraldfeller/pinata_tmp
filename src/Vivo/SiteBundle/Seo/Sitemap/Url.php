<?php

namespace Vivo\SiteBundle\Seo\Sitemap;

class Url implements UrlInterface
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var \DateTime
     */
    protected $lastModified;

    /**
     * @var string
     */
    protected $changeFrequency;

    /**
     * @var float
     */
    protected $priority;

    /**
     * Constructor.
     *
     * @param string|null    $location
     * @param \DateTime|null $lastModified
     * @param string|null    $changeFrequency
     * @param float|null     $priority
     */
    public function __construct($location = null, $lastModified = null, $changeFrequency = null, $priority = null)
    {
        if (null !== $location) {
            $this->setLocation($location);
        }

        if (null !== $lastModified) {
            $this->setLastModified($lastModified);
        }

        if (null !== $changeFrequency) {
            $this->setChangeFrequency($changeFrequency);
        }

        if (null !== $priority) {
            $this->setPriority($priority);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setLocation($location)
    {
        $this->location = null === $location ? $location : (string) $location;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastModified(\DateTime $lastModified = null)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified()
    {
        return null === $this->lastModified ? null : $this->lastModified;
    }

    /**
     * {@inheritdoc}
     */
    public function setChangeFrequency($changeFrequency)
    {
        $this->changeFrequency = null === $changeFrequency ? $changeFrequency : (string) $changeFrequency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChangeFrequency()
    {
        return $this->changeFrequency;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = null === $priority ? $priority : (float) $priority;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
