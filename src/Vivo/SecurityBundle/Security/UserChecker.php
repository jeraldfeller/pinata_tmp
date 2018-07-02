<?php

namespace Vivo\SecurityBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserChecker as BaseUserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Vivo\SecurityBundle\Exception\ExceedDailyIpAddressLimitException;
use Vivo\SecurityBundle\Exception\ExceedDailyUsernameLimitException;
use Vivo\SecurityBundle\Exception\ExceedHourlyIpAddressLimitException;
use Vivo\SecurityBundle\Exception\ExceedHourlyUsernameAndIpLimitException;
use Vivo\SecurityBundle\Repository\AuthenticationLogRepositoryInterface;
use Vivo\SecurityBundle\Util\FirewallManager;

class UserChecker extends BaseUserChecker
{
    /**
     * @var FirewallManager
     */
    private $firewallManager;

    /**
     * @var AuthenticationLogRepositoryInterface
     */
    private $authenticationLogRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FirewallManager $firewallManager
     */
    public function setFirewallManager(FirewallManager $firewallManager)
    {
        $this->firewallManager = $firewallManager;
    }

    /**
     * @param AuthenticationLogRepositoryInterface $authenticationLogRepository
     */
    public function setAuthenticationLogRepository(AuthenticationLogRepositoryInterface $authenticationLogRepository)
    {
        $this->authenticationLogRepository = $authenticationLogRepository;
    }

    /**
     * @param RequestStack $requestStack
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function checkPreAuth(UserInterface $user)
    {
        parent::checkPreAuth($user);

        $currentUser = $this->getCurrentUser();

        if (null === $currentUser || $user !== $currentUser) {
            $this->checkLimits($user->getUsername());
        }
    }

    /**
     * @param string $username
     */
    public function checkLimits($username)
    {
        if (!$this->getFirewallConfig($this->getCurrentFirewallName())->isEnabled()) {
            return;
        }

        if (strlen($username) > 0) {
            $this->checkHourlyIpAndUsernameLimit($username);
            $this->checkDailyUsernameLimit($username);
        }

        $this->checkHourlyIpAddressLimit();
        $this->checkDailyIpAddressLimit();
    }

    /**
     * @param string $username
     *
     * @throws ExceedHourlyUsernameAndIpLimitException
     */
    protected function checkHourlyIpAndUsernameLimit($username)
    {
        $firewallName = $this->getCurrentFirewallName();
        $ipAddress = $this->getClientIpAddress();
        $maxFailCount = $this->getFirewallConfig($firewallName)->getHourlyIpAndUsernameLimit();

        $checkFrom = new \DateTime('-1 hour');
        if (null !== $lastSuccess = $this->getAuthenticationLogRepository()->getLastSucessfulAttemptAt($firewallName, $ipAddress, $username)) {
            $checkFrom = max($checkFrom, $lastSuccess);
        }

        $failureCount = $this->getAuthenticationLogRepository()->countIpAndUsernameFailuresSince($firewallName, $ipAddress, $username, $checkFrom);

        if ($failureCount >= $maxFailCount) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf(
                    "User '%s' from '%s' has been blocked from firewall '%s'. %s failed logins since %s",
                    $username,
                    $ipAddress,
                    $firewallName,
                    number_format($failureCount, 0),
                    $checkFrom->format('Y-m-d H:i:s')
                ));
            }

            throw new ExceedHourlyUsernameAndIpLimitException();
        }
    }

    /**
     * @param string $username
     *
     * @throws ExceedDailyUsernameLimitException
     */
    protected function checkDailyUsernameLimit($username)
    {
        $firewallName = $this->getCurrentFirewallName();
        $maxFailCount = $this->getFirewallConfig($firewallName)->getDailyUsernameLimit();
        $checkFrom = new \DateTime('-24 hours');
        $failureCount = $this->getAuthenticationLogRepository()->countUsernameFailuresSince($firewallName, $username, $checkFrom);

        if ($failureCount >= $maxFailCount) {
            if (null !== $this->logger) {
                $this->logger->alert(sprintf(
                    "User '%s' from all ip addresses has been blocked from firewall '%s'. %s failed logins since %s",
                    $username,
                    $firewallName,
                    number_format($failureCount, 0),
                    $checkFrom->format('Y-m-d H:i:s')
                ));
            }

            throw new ExceedDailyUsernameLimitException();
        }
    }

    /**
     * @throws ExceedHourlyIpAddressLimitException
     */
    protected function checkHourlyIpAddressLimit()
    {
        $firewallName = $this->getCurrentFirewallName();
        $ipAddress = $this->getClientIpAddress();
        $maxFailureCount = $this->getFirewallConfig($firewallName)->getHourlyIpAddressLimit();
        $ipFailuresSince = new \DateTime('-1 hour');
        $ipFailureCount = $this->getAuthenticationLogRepository()->countIpFailuresSince($firewallName, $ipAddress, $ipFailuresSince);

        if ($ipFailureCount >= $maxFailureCount) {
            if (null !== $this->logger) {
                $this->logger->alert(sprintf(
                    "Ip Address '%s' has been blocked from firewall '%s'. %s failed logins since %s",
                    $ipAddress,
                    $firewallName,
                    number_format($ipFailureCount, 0),
                    $ipFailuresSince->format('Y-m-d H:i:s')
                ));
            }

            throw new ExceedHourlyIpAddressLimitException();
        }
    }

    /**
     * @throws ExceedDailyIpAddressLimitException
     */
    protected function checkDailyIpAddressLimit()
    {
        $firewallName = $this->getCurrentFirewallName();
        $ipAddress = $this->getClientIpAddress();
        $maxFailureCount = $this->getFirewallConfig($firewallName)->getDailyIpAddressLimit();
        $ipFailuresSince = new \DateTime('-24 hours');
        $ipFailureCount = $this->getAuthenticationLogRepository()->countIpFailuresSince($firewallName, $ipAddress, $ipFailuresSince);

        if ($ipFailureCount >= $maxFailureCount) {
            if (null !== $this->logger) {
                // Make every 50th attempt Alert so it is caught by Swiftmailer
                $this->logger->alert(sprintf(
                    "Ip Address '%s' has been blocked from firewall '%s'. %s failed logins since %s",
                    $ipAddress,
                    $firewallName,
                    number_format($ipFailureCount, 0),
                    $ipFailuresSince->format('Y-m-d H:i:s')
                ));
            }

            throw new ExceedDailyIpAddressLimitException();
        }
    }

    /**
     * @return AuthenticationLogRepositoryInterface
     *
     * @throws \Exception
     */
    protected function getAuthenticationLogRepository()
    {
        if (null === $this->authenticationLogRepository) {
            throw new \Exception('Authentication log repository is not set.');
        }

        return $this->authenticationLogRepository;
    }

    /**
     * Get client ip address.
     *
     * @return null|string
     */
    protected function getClientIpAddress()
    {
        if (null === $this->requestStack) {
            throw new \Exception('RequestStack is not set.');
        }

        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $request->getClientIp();
        }

        return;
    }

    /**
     * Get current firewall name.
     *
     * @return string
     */
    public function getCurrentFirewallName()
    {
        if (null === $this->requestStack) {
            throw new \Exception('RequestStack is not set.');
        }

        if (null === $this->firewallManager) {
            throw new \Exception('FirewallManager is not set.');
        }

        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $this->firewallManager->getFirewallNameForRequest($request);
        }

        return;
    }

    /**
     * Returns the current user.
     *
     * @return mixed
     *
     * @see TokenInterface::getUser()
     */
    protected function getCurrentUser()
    {
        if (null === $this->tokenStorage) {
            throw new \Exception('TokenStorage is not set.');
        }

        if (!$token = $this->tokenStorage->getToken()) {
            return;
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            return;
        }

        return $user;
    }

    /**
     * @param string $firewallName
     *
     * @return FirewallConfig
     */
    protected function getFirewallConfig($firewallName)
    {
        return $this->firewallManager->getConfig($firewallName);
    }
}
