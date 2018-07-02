<?php

namespace Vivo\PageBundle\Routing;

use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;
use Vivo\PageBundle\Repository\PageRepositoryInterface;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SlugBundle\Model\SlugInterface;
use Doctrine\Common\Cache\Cache;

class RouteProvider implements RouteProviderInterface
{
    /**
     * Cache key to store routes.
     */
    const CACHE_KEY = 'vivo_page.routing';

    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @var \Vivo\PageBundle\Repository\PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var \Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface
     */
    protected $pageTypeManager;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var RouteCollection
     */
    private $collection;

    /**
     * @param SiteFactory              $siteFactory
     * @param PageRepositoryInterface  $pageRepository
     * @param PageTypeManagerInterface $pageTypeManager
     * @param Cache                    $cache
     */
    public function __construct(
        SiteFactory $siteFactory = null,
        PageRepositoryInterface $pageRepository,
        PageTypeManagerInterface $pageTypeManager,
        Cache $cache
    ) {
        $this->siteFactory = $siteFactory;
        $this->pageRepository = $pageRepository;
        $this->pageTypeManager = $pageTypeManager;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        return $this->getCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteByName($name)
    {
        if (0 !== strpos($name, SlugInterface::TEMP_SLUG_PREFIX)) {
            $collection = $this->getCollection();

            if ($route = $collection->get($name)) {
                return $route;
            }
        }

        throw new RouteNotFoundException(sprintf("No route found for path '%s'", $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutesByNames($names)
    {
        if (null === $names) {
            return $this->getCollection();
        }

        $routes = array();

        foreach ($names as $name) {
            try {
                $routes[] = $this->getRouteByName($name);
            } catch (RouteNotFoundException $e) {
            }
        }

        return $routes;
    }

    /**
     * @return RouteCollection
     */
    protected function getCollection()
    {
        if (null !== $this->collection) {
            return $this->collection;
        }

        $site = $this->siteFactory->get();
        $cacheId = self::CACHE_KEY.'_'.(null !== $site ? $site->getId() : '0');

        if ($this->cache->contains($cacheId)) {
            return $this->collection = $this->cache->fetch($cacheId);
        }

        $pages = $this->pageRepository->findAllWithSlugs();

        /** @var RouteCollection|Route[] $collection */
        $collection = $this->generateRouteCollectionFromResults($pages);

        $catchAllRoutes = new RouteCollection();
        $redirectRoutes = new RouteCollection();

        foreach ($pages as $page) {
            foreach ($page->getSlugs() as $slug) {
                $catchAllRouteName = SlugInterface::TEMP_SLUG_PREFIX.'catch_all_'.$slug->getId();
                $catchAllRoute = new Route($slug->getSlug().'/{uri}', array(
                        '_controller' => 'VivoPageBundle:Page:catchAll',
                        '_route' => $catchAllRouteName, // Uses to determine if a slug is in use
                        'cmsPage' => $page,
                    ), array(
                        'uri' => '.+',
                    ));

                $redirectRouteName = SlugInterface::TEMP_SLUG_PREFIX.'old_slug_'.$slug->getId();
                $redirectRoute = new Route($slug->getSlug(), array(
                    '_controller' => 'FrameworkBundle:Redirect:redirect',
                    '_route' => $redirectRouteName, // Uses to determine if a slug is in use
                    'route' => $page->getPageTypeInstance()->getRouteName(),
                    'permanent' => true,
                ));

                $catchAllRoutes->add($catchAllRouteName, $catchAllRoute);
                $redirectRoutes->add($redirectRouteName, $redirectRoute);
            }
        }

        foreach ($collection as $routeName => $route) {
            $schemes = $route->getSchemes();

            if (1 === count($schemes)) {
                $scheme = reset($schemes);

                if ('https' === $scheme) {
                    $route = clone $route;
                    $route->setSchemes(array());

                    /*
                     * Let Vivo\SiteBundle\EventListener\RequiresChannelListener handle the redirect.
                     */
                    $route->setRequirement('_requires_channel', 'https');

                    $collection->add('_non_secure_'.$routeName, $route);
                }
            }
        }

        $collection->addCollection($this->sortRouteCollectionLongestToShortest($redirectRoutes));
        $collection->addCollection($this->sortRouteCollectionLongestToShortest($catchAllRoutes));

        foreach ($collection as $routeName => $route) {
            // Let's convert all the entities to entity references instead
            $defaults = $route->getDefaults();

            foreach ($defaults as $key => $value) {
                if (is_object($value) && $value instanceof PageInterface) {
                    $route->setDefault($key, new PageReference($value));
                }
            }
        }

        $this->cache->save($cacheId, $collection, 3600);

        return $this->collection = $collection;
    }

    /**
     * @param PageInterface[] $pages
     *
     * @return RouteCollection
     *
     * @throws \UnexpectedValueException
     */
    protected function generateRouteCollectionFromResults(array $pages)
    {
        $pageRouteCollection = new RouteCollection();

        foreach ($pages as $page) {
            if (!$page instanceof PageInterface) {
                throw new \UnexpectedValueException('$page must implement Vivo\\PageBundle\\Entity\\PageInterface');
            }

            if ($page->getPageTypeInstance()) {
                foreach ($page->getPageTypeInstance()->getRouteCollection() as $routeName => $route) {
                    if (!$route->hasDefault('_route')) {
                        $route->setDefault('_route', $routeName);
                    }

                    $pageRouteCollection->add($routeName, $route);
                }
            }
        }

        $collection = new RouteCollection();
        foreach ($this->pageTypeManager->getPageTypes() as $pageType) {
            foreach ($pageType->getRouteCollection() as $routeName => $route) {
                if (null === $pageRouteCollection->get($routeName)) {
                    if (!$route->hasDefault('_route')) {
                        $route->setDefault('_route', $routeName);
                    }

                    $route->setDefault('vivo_page_fallback', true);
                    $collection->add($routeName, $route);
                }
            }
        }

        $collection->addCollection($pageRouteCollection);

        return $collection;
    }

    /**
     * Sorts the route collection from longest path to shortest.
     *
     * @param RouteCollection $collection
     *
     * @return RouteCollection
     */
    protected function sortRouteCollectionLongestToShortest(RouteCollection $collection)
    {
        $catchAllIterator = $collection->getIterator();
        $catchAllIterator->uasort(function (Route $first, Route $second) {
            return strlen($first->getPath()) > strlen($second->getPath()) ? -1 : 1;
        });

        $collection = new RouteCollection();
        foreach ($catchAllIterator as $routeName => $route) {
            $collection->add($routeName, $route);
        }

        return $collection;
    }
}
