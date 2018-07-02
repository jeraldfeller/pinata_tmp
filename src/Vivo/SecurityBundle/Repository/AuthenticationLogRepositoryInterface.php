<?php

namespace Vivo\SecurityBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

interface AuthenticationLogRepositoryInterface extends ObjectRepository
{
    /**
     * Purge old logs.
     *
     * @return int
     */
    public function purge();

    /**
     * Count the number of failed logins for a specific ip.
     *
     * @param string    $firewallName
     * @param string    $username
     * @param \DateTime $from
     *
     * @return int
     */
    public function countIpFailuresSince($firewallName, $ipAddress, \DateTime $from);

    /**
     * Count the the number of failed logins for a ipAddress and username.
     *
     * @param string    $firewallName
     * @param string    $ipAddress
     * @param string    $username
     * @param \DateTime $from
     *
     * @return int
     */
    public function countIpAndUsernameFailuresSince($firewallName, $ipAddress, $username, \DateTime $from);

    /**
     * Count the the number of failed logins for a username.
     *
     * @param string    $firewallName
     * @param string    $username
     * @param \DateTime $from
     *
     * @return int
     */
    public function countUsernameFailuresSince($firewallName, $username, \DateTime $from);

    /**
     * Get the last successful attempt.
     *
     * @param string $firewallName
     * @param string $ipAddress
     * @param string $username
     *
     * @return \DateTime
     */
    public function getLastSucessfulAttemptAt($firewallName, $ipAddress, $username);

    /**
     * Creates a new instance of UserInterface.
     *
     * @return \Vivo\SecurityBundle\Model\AuthenticationLog
     */
    public function createAuthenticationLog();
}
