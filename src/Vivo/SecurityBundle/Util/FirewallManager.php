<?php

namespace Vivo\SecurityBundle\Util;

use Symfony\Component\HttpFoundation\Request;
use Vivo\SecurityBundle\Security\FirewallConfig;

class FirewallManager
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestMatcher[]
     */
    private $map;

    /**
     * @var array
     */
    private $defaultConfig = [];

    /**
     * @var array[]
     */
    private $firewallConfigs = [];

    /**
     * @var FirewallConfig[]
     */
    private $config = [];

    /**
     * @param \Symfony\Component\HttpFoundation\RequestMatcher[] $map
     * @param array                                              $defaultConfig
     * @param array                                              $firewallConfigs
     */
    public function __construct(array $map, array $defaultConfig, array $firewallConfigs)
    {
        $this->map = $map;
        $this->defaultConfig = $defaultConfig;
        $this->firewallConfig = $firewallConfigs;
    }

    /**
     * Get firewall config.
     *
     * @param Request $request
     *
     * @return FirewallConfig
     */
    public function getConfig($firewallName)
    {
        if (!array_key_exists($firewallName, $this->config)) {
            $config = new FirewallConfig();

            $firewallConfig = array_key_exists($firewallName, $this->firewallConfigs) ? $this->firewallConfigs[$firewallName] : $this->defaultConfig;

            $config->setEnabled($firewallConfig['enabled']);
            $config->setHourlyIpAndUsernameLimit($firewallConfig['hourly_ip_username_limit']);
            $config->setDailyUsernameLimit($firewallConfig['daily_username_limit']);
            $config->setHourlyIpAddressLimit($firewallConfig['hourly_ip_limit']);
            $config->setDailyIpAddressLimit($firewallConfig['daily_ip_limit']);

            $this->config[$firewallName] = $config;
        }

        return $this->config[$firewallName];
    }

    /**
     * Get config for a specific request.
     *
     * @param Request $request
     *
     * @return FirewallConfig
     */
    public function getConfigForRequest(Request $request)
    {
        return $this->getConfig($this->getFirewallNameForRequest($request));
    }

    /**
     * Get firewall name for Request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    public function getFirewallNameForRequest(Request $request)
    {
        foreach ($this->map as $firewallName => $requestMatcher) {
            if (null === $requestMatcher || $requestMatcher->matches($request)) {
                return $firewallName;
            }
        }

        return;
    }
}
