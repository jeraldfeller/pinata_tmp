<?php

namespace Vivo\AdminBundle\Security;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Vivo\AdminBundle\Model\UserInterface;

/**
 * Class UserAuthenticator.
 *
 * Authenticate user programmatically
 */
class UserAuthenticator
{
    /**
     * @var string
     */
    protected $firewallProviderKey;

    /**
     * @var TokenStorageInterface
     */
    protected $authTokenStorage;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param $firewallProviderKey
     * @param TokenStorageInterface    $authTokenStorage
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct($firewallProviderKey, TokenStorageInterface $authTokenStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->firewallProviderKey = $firewallProviderKey;
        $this->authTokenStorage = $authTokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Authenticate the user.
     *
     * @param UserInterface $user
     * @param Request       $request
     */
    public function authenticateUser(UserInterface $user, Request $request)
    {
        $token = new UsernamePasswordToken($user, null, $this->firewallProviderKey, $user->getRoles());
        $this->authTokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }
}
