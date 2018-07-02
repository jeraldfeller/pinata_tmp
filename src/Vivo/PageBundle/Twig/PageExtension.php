<?php

namespace Vivo\PageBundle\Twig;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;

class PageExtension extends \Twig_Extension
{
    /**
     * @var \Vivo\PageBundle\Repository\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param PageRepositoryInterface $pageRepository
     * @param UrlGeneratorInterface   $generator
     */
    public function __construct(PageRepositoryInterface $pageRepository, UrlGeneratorInterface $generator)
    {
        $this->pageRepository = $pageRepository;
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('route_exists', array($this, 'routeExists')),
        );
    }

    /**
     * Checks if a route exists.
     *
     * @param $name
     * @param array $parameters
     * @param bool  $relative
     *
     * @return bool
     */
    public function routeExists($name, $parameters = array(), $relative = false)
    {
        try {
            return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (RouteNotFoundException $e) {
        }

        return false;
    }

    /**
     * @param $alias
     *
     * @return null|\Vivo\PageBundle\Model\PageInterface
     */
    public function getPageByAlias($alias)
    {
        return $this->pageRepository->findOnePageByAlias($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_page_page_extension';
    }
}
