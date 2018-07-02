<?php

namespace Vivo\SecurityBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Vivo\SecurityBundle\Model\AuthenticationLogInterface;
use Vivo\SecurityBundle\Repository\AuthenticationLogRepositoryInterface;
use Vivo\SecurityBundle\Security\UserChecker;
use Vivo\SecurityBundle\Util\FirewallManager;
use Vivo\UtilBundle\Util\GeoIp;

class AuthenticationListener
{
    /**
     * @var FirewallManager
     */
    protected $firewallManager;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var AuthenticationLogRepositoryInterface
     */
    protected $authenticationLogRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var UserChecker
     */
    protected $userChecker;

    /**
     * @var GeoIp
     */
    protected $geoIp;

    /**
     * @param FirewallManager                      $firewallManager
     * @param RequestStack                         $requestStack
     * @param AuthenticationLogRepositoryInterface $authenticationLogRepository
     * @param EntityManagerInterface               $em
     * @param UserChecker                          $userChecker
     * @param GeoIp                                $geoIp
     */
    public function __construct(
        FirewallManager $firewallManager,
        RequestStack $requestStack,
        AuthenticationLogRepositoryInterface $authenticationLogRepository,
        EntityManagerInterface $em,
        UserChecker $userChecker,
        GeoIp $geoIp
    ) {
        $this->firewallManager = $firewallManager;
        $this->requestStack = $requestStack;
        $this->authenticationLogRepository = $authenticationLogRepository;
        $this->em = $em;
        $this->userChecker = $userChecker;
        $this->geoIp = $geoIp;
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        if (!$this->firewallManager->getConfigForRequest($request)->isEnabled()) {
            return;
        }

        $token = $event->getAuthenticationToken();

        // Failed attempts won't reach the user check. Let's fire it ourselves
        $this->userChecker->checkLimits($token->getUsername());

        $authenticationLog = $this->createAuthenticationLog($request);
        $authenticationLog->setStatus(AuthenticationLogInterface::STATUS_FAILURE);
        $authenticationLog->setUsername($token->getUsername());

        $this->purgeLogs();
        $this->em->persist($authenticationLog);
        $this->em->flush();
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        if (!$this->firewallManager->getConfigForRequest($request)->isEnabled()) {
            return;
        }

        $token = $event->getAuthenticationToken();

        $authenticationLog = $this->createAuthenticationLog($request);
        $authenticationLog->setStatus(AuthenticationLogInterface::STATUS_SUCCESS);
        $authenticationLog->setUsername($token->getUsername());

        $this->purgeLogs();
        $this->em->persist($authenticationLog);
        $this->em->flush();
    }

    /**
     * @return \Vivo\SecurityBundle\Model\AuthenticationLog
     */
    protected function createAuthenticationLog(Request $request)
    {
        $ipAddress = $request->getClientIp();
        $city = $this->geoIp->getCity($ipAddress);

        $authenticationLog = $this->authenticationLogRepository->createAuthenticationLog();

        $authenticationLog->setFirewallName($this->firewallManager->getFirewallNameForRequest($request));
        $authenticationLog->setIpAddress($ipAddress);
        $authenticationLog->setLoggedAt(new \DateTime('now'));
        $authenticationLog->setIpCountry($city->country->isoCode);
        $authenticationLog->setIpSubdivision($city->mostSpecificSubdivision->isoCode);
        $authenticationLog->setIpCity($city->city->name);

        return $authenticationLog;
    }

    protected function purgeLogs()
    {
        $rand = (int) rand(1, 5);

        if (1 === $rand) {
            // We don't want to purge logs for every time listener fires
            $this->authenticationLogRepository->purge();
        }
    }
}
