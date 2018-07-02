<?php

namespace Vivo\SiteBundle\Session;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class Storage extends NativeSessionStorage
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param ContainerInterface $container
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->started && !$this->closed) {
            return true;
        }

        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            if ('vivo_site.admin.site.switch' === $request->attributes->get('_route')) {
                if ($request->query->has('sessionId') && $sessionId = $request->query->get('sessionId')) {
                    session_id($request->query->get('sessionId'));
                }
            }
        }

        parent::start();
    }
}
