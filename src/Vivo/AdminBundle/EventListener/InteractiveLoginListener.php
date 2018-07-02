<?php

namespace Vivo\AdminBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Vivo\AdminBundle\Model\UserInterface;

class InteractiveLoginListener
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @param EntityManager $entityManager
     * @param RequestStack  $requestStack
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserInterface) {
            $user->setLastLoginAt(new \DateTime('now'));
            $user->setLastLoginFrom($request->getClientIp());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
