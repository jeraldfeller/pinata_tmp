<?php

namespace Vivo\AssetBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;

class TrimPathExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('trim_path', array($this, 'trimPath')),
        );
    }

    /**
     * Trim path.
     *
     * @param string $path
     *
     * @return string
     */
    public function trimPath($path)
    {
        $request = $this->requestStack->getCurrentRequest();

        // Strip scheme and hostname
        $path = preg_replace('/^'.preg_quote($request->getSchemeAndHttpHost(), '/').'/', '', $path);

        // Strip current base url
        $path = preg_replace('/^'.preg_quote($request->getBaseUrl(), '/').'/', '', $path);
        // Strip base url without app_dev.php
        $path = preg_replace('/^'.preg_quote(preg_replace('/app_dev.php$/', '', $request->getBaseUrl()), '/').'/', '', $path);

        return ltrim($path, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_asset_trim_path';
    }
}
