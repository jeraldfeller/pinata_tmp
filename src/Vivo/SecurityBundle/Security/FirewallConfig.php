<?php

namespace Vivo\SecurityBundle\Security;

class FirewallConfig
{
    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var int
     */
    private $hourlyIpAndUsernameLimit = 0;

    /**
     * @var int
     */
    private $dailyUsernameLimit = 0;

    /**
     * @var int
     */
    private $hourlyIpAddressLimit = 0;

    /**
     * @var int
     */
    private $dailyIpAddressLimit = 0;

    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set hourlyIpAndUsernameLimit.
     *
     * @param int $hourlyIpAndUsernameLimit
     *
     * @return $this
     */
    public function setHourlyIpAndUsernameLimit($hourlyIpAndUsernameLimit)
    {
        $this->hourlyIpAndUsernameLimit = (int) $hourlyIpAndUsernameLimit;

        return $this;
    }

    /**
     * Get hourlyIpAndUsernameLimit.
     *
     * @return int
     */
    public function getHourlyIpAndUsernameLimit()
    {
        return $this->hourlyIpAndUsernameLimit;
    }

    /**
     * Set dailyUsernameLimit.
     *
     * @param int $dailyUsernameLimit
     *
     * @return $this
     */
    public function setDailyUsernameLimit($dailyUsernameLimit)
    {
        $this->dailyUsernameLimit = (int) $dailyUsernameLimit;

        return $this;
    }

    /**
     * Get dailyUsernameLimit.
     *
     * @return int
     */
    public function getDailyUsernameLimit()
    {
        return $this->dailyUsernameLimit;
    }

    /**
     * Set hourlyIpAddressLimit.
     *
     * @param int $hourlyIpAddressLimit
     *
     * @return $this
     */
    public function setHourlyIpAddressLimit($hourlyIpAddressLimit)
    {
        $this->hourlyIpAddressLimit = (int) $hourlyIpAddressLimit;

        return $this;
    }

    /**
     * Get hourlyIpAddressLimit.
     *
     * @return int
     */
    public function getHourlyIpAddressLimit()
    {
        return $this->hourlyIpAddressLimit;
    }

    /**
     * Set dailyIpAddressLimit.
     *
     * @param int $dailyIpAddressLimit
     *
     * @return $this
     */
    public function setDailyIpAddressLimit($dailyIpAddressLimit)
    {
        $this->dailyIpAddressLimit = (int) $dailyIpAddressLimit;

        return $this;
    }

    /**
     * Get dailyIpAddressLimit.
     *
     * @return int
     */
    public function getDailyIpAddressLimit()
    {
        return $this->dailyIpAddressLimit;
    }
}
