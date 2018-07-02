<?php

namespace Vivo\SiteBundle\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Vivo\SiteBundle\Factory\SiteFactory;

class SitePasswordListener
{
    /**
     * @var SiteFactory
     */
    protected $siteFactory;

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
     * @var array
     */
    protected $passwords;

    /**
     * @var string
     */
    protected $passwordRoute;

    /**
     * @var string
     */
    protected $targetQueryParameter;

    /**
     * @var string
     */
    protected $csrfQueryParameter;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var Esi
     */
    protected $esi;

    /**
     * Constructor.
     *
     * @param SiteFactory               $siteFactory
     * @param RouterInterface           $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param bool                      $enabled
     * @param array                     $passwords
     * @param string                    $passwordRoute
     * @param string                    $targetQueryParameter
     * @param string                    $csrfQueryParameter
     * @param string                    $salt
     * @param Esi                       $esi
     */
    public function __construct(
        SiteFactory $siteFactory,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        $enabled,
        array $passwords,
        $passwordRoute,
        $targetQueryParameter,
        $csrfQueryParameter,
        $salt,
        Esi $esi = null
    ) {
        $this->siteFactory = $siteFactory;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->enabled = $enabled;
        $this->passwords = $passwords;
        $this->passwordRoute = $passwordRoute;
        $this->targetQueryParameter = $targetQueryParameter;
        $this->csrfQueryParameter = $csrfQueryParameter;
        $this->salt = $salt;
        $this->esi = $esi;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->enabled) {
            return;
        }

        $request = $event->getRequest();

        if (null === $this->esi || !$this->esi->hasSurrogateCapability($request)) {
            return;
        }

        $site = $this->siteFactory->get();
        if (null === $site || $site->isLive()) {
            return;
        }

        if (null !== $clientPassword = $request->get('password', null)) {
            $clientPassword = $this->encodePassword($clientPassword);
        } elseif (null === $clientPassword = $request->cookies->get('vivo_site_pw', null)) {
            return;
        }

        $response = $event->getResponse();
        if ($xHash = $response->headers->get('X-Hash', '')) {
            $xHash .= '-';
        }

        $response->headers->set('X-Hash', $xHash.$this->checkPassword($clientPassword));
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->handleResponseEvent($event);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->handleResponseEvent($event);
    }

    /**
     * @param GetResponseEvent $event
     */
    protected function handleResponseEvent(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        $allowedRoutes = array(
            '^(_wdt|^_profiler)$',
            '^(_assetic_|liip_imagine_filter)',
            '^(vivo_site.site.(catch_)?flush)$',
        );

        if (preg_match('/'.implode('|', $allowedRoutes).'/i', $route)) {
            return;
        }

        $site = $this->siteFactory->get();

        if (!$site) {
            return;
        }

        if ($site->isLive() || !$this->enabled) {
            if ($route === $this->passwordRoute) {
                $event->setResponse(new RedirectResponse($this->getTargetUrl($request)));
                $event->stopPropagation();
            }

            return;
        }

        $redirectOnSuccess = false;
        $clientPassword = null;

        if (null !== $clientPassword = $request->get('password', null)) {
            $clientPassword = $this->encodePassword($clientPassword);
            $redirectOnSuccess = true;
        } else {
            $clientPassword = $request->cookies->get('vivo_site_pw', null);

            if ($route === $this->passwordRoute) {
                $redirectOnSuccess = true;
            }
        }

        if ($clientPassword) {
            if (true === $this->checkPassword($clientPassword)) {
                if ($redirectOnSuccess) {
                    $response = new RedirectResponse($this->getTargetUrl($request));
                    $response->headers->setCookie(new Cookie('vivo_site_pw', $clientPassword, strtotime('+30 days 23:59:59')));

                    $event->setResponse($response);
                    $event->stopPropagation();
                }

                return;
            }
        }

        if ($route === $this->passwordRoute) {
            return;
        }

        $url = $this->router->generate($this->passwordRoute, array(
            $this->targetQueryParameter => $request->getRequestUri(),
            $this->csrfQueryParameter => (string) $this->csrfTokenManager->getToken($request->getRequestUri()),
        ));

        if ($url) {
            $event->setResponse(new RedirectResponse($url));
            $event->stopPropagation();
        }
    }

    /**
     * Returns the target url.
     *
     * @return string
     */
    protected function getTargetUrl(Request $request)
    {
        if ($request->query->get($this->targetQueryParameter) && $this->csrfTokenManager->isTokenValid(new CsrfToken($request->query->get($this->targetQueryParameter), $request->query->get($this->csrfQueryParameter)))) {
            $url = $request->query->get($this->targetQueryParameter);
        } else {
            $url = $this->router->generate('homepage');
        }

        return $url;
    }

    /**
     * Encodes the password.
     *
     * @param $password
     *
     * @return string
     */
    protected function encodePassword($password)
    {
        $site = $this->siteFactory->get();

        return hash('sha256', $password.'_'.($site ? $site->getId() : '').'_'.get_class($this).$this->salt);
    }

    /**
     * Check password is correct.
     *
     * @param $clientPassword
     *
     * @return bool
     */
    protected function checkPassword($clientPassword)
    {
        foreach ($this->passwords as $password) {
            if (true === hash_equals($clientPassword, $this->encodePassword($password))) {
                return true;
            }
        }

        return false;
    }
}
