<?php

namespace Vivo\AdminBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Vivo\AdminBundle\Model\UserInterface;

class ExpiredPasswordListener
{
    /**
     * @var TokenStorageInterface
     */
    protected $authTokenStorage;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $expiredRoute;

    /**
     * @var string
     */
    protected $targetQueryParameter;

    /**
     * @var string
     */
    protected $csrfQueryParameter;

    /**
     * @param TokenStorageInterface     $authTokenStorage
     * @param RouterInterface           $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param bool                      $enabled
     * @param string                    $expiredRoute
     * @param string                    $targetQueryParameter
     * @param string                    $csrfQueryParameter
     */
    public function __construct(
        TokenStorageInterface $authTokenStorage,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        $enabled,
        $expiredRoute,
        $targetQueryParameter,
        $csrfQueryParameter)
    {
        $this->authTokenStorage = $authTokenStorage;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->enabled = $enabled;
        $this->expiredRoute = $expiredRoute;
        $this->targetQueryParameter = $targetQueryParameter;
        $this->csrfQueryParameter = $csrfQueryParameter;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest() || !$this->enabled) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (in_array($route, array($this->expiredRoute, '_wdt', '_profiler'), true)) {
            return;
        }

        if ($token = $this->authTokenStorage->getToken()) {
            $user = $token->getUser();

            if (null === $user || !$user instanceof UserInterface || true !== $user->isPasswordExpired()) {
                return;
            }

            $redirectTo = $this->router->generate($this->expiredRoute, array(
                $this->targetQueryParameter => $request->getRequestUri(),
                $this->csrfQueryParameter => (string) $this->csrfTokenManager->getToken($request->getRequestUri()),
            ));

            if ($redirectTo) {
                $event->setResponse(new RedirectResponse($redirectTo));
            }
        }
    }
}
