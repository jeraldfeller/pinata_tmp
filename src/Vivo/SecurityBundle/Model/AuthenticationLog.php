<?php

namespace Vivo\SecurityBundle\Model;

/**
 * AuthenticationLog.
 */
class AuthenticationLog implements AuthenticationLogInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $firewallName;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var string
     */
    protected $ipCountry;

    /**
     * @var string
     */
    protected $ipSubdivision;

    /**
     * @var string
     */
    protected $ipCity;

    /**
     * @var \DateTime
     */
    protected $loggedAt;

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
    public function setStatus($status)
    {
        $this->status = (int) $status;

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
    public function setFirewallName($firewallName)
    {
        $this->firewallName = $firewallName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirewallName()
    {
        return $this->firewallName;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = strlen($username) < 1 ? null : $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setIpCountry($ipCountry)
    {
        $this->ipCountry = strlen($ipCountry) < 1 ? null : $ipCountry;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIpCountry()
    {
        return $this->ipCountry;
    }

    /**
     * {@inheritdoc}
     */
    public function setIpSubdivision($ipSubdivision)
    {
        $this->ipSubdivision = strlen($ipSubdivision) < 1 ? null : $ipSubdivision;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIpSubdivision()
    {
        return $this->ipSubdivision;
    }

    /**
     * {@inheritdoc}
     */
    public function setIpCity($ipCity)
    {
        $this->ipCity = strlen($ipCity) < 1 ? null : $ipCity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIpCity()
    {
        return $this->ipCity;
    }

    /**
     * {@inheritdoc}
     */
    public function setLoggedAt(\DateTime $loggedAt)
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoggedAt()
    {
        return $this->loggedAt;
    }
}
