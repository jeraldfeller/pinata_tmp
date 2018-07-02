<?php

namespace Vivo\SecurityBundle\Model;

interface AuthenticationLogInterface
{
    const STATUS_FAILURE = 0;
    const STATUS_SUCCESS = 1;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set status.
     *
     * @param int $status
     */
    public function setStatus($status);

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set firewallName.
     *
     * @param string $firewallName
     *
     * @return $this
     */
    public function setFirewallName($firewallName);

    /**
     * Get firewallName.
     *
     * @return string
     */
    public function getFirewallName();

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username);

    /**
     * Get username.
     *
     * @return string|null
     */
    public function getUsername();

    /**
     * Set ipAddress.
     *
     * @param string $ipAddress
     *
     * @return $this
     */
    public function setIpAddress($ipAddress);

    /**
     * Get ipAddress.
     *
     * @return string
     */
    public function getIpAddress();

    /**
     * Set ipCountry.
     *
     * @param string $ipCountry
     *
     * @return $this
     */
    public function setIpCountry($ipCountry);

    /**
     * Get ipCountry.
     *
     * @return string
     */
    public function getIpCountry();

    /**
     * Set ipSubdivision.
     *
     * @param string $ipSubdivision
     *
     * @return $this
     */
    public function setIpSubdivision($ipSubdivision);

    /**
     * Get ipSubdivision.
     *
     * @return string
     */
    public function getIpSubdivision();

    /**
     * Set ipCity.
     *
     * @param string $ipCity
     *
     * @return $this
     */
    public function setIpCity($ipCity);

    /**
     * Get ipCity.
     *
     * @return string
     */
    public function getIpCity();

    /**
     * Set loggedAt.
     *
     * @param \DateTime $loggedAt
     *
     * @return $this
     */
    public function setLoggedAt(\DateTime $loggedAt);

    /**
     * Get loggedAt.
     *
     * @return \DateTime
     */
    public function getLoggedAt();
}
