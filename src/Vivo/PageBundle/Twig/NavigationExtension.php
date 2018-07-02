<?php

namespace Vivo\PageBundle\Twig;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Vivo\PageBundle\Menu\Event\Events;
use Vivo\PageBundle\Menu\Event\ItemEvent;
use Vivo\PageBundle\Menu\Item;
use Vivo\PageBundle\Menu\ItemInterface;
use Vivo\PageBundle\Model\MenuNodeInterface;
use Vivo\PageBundle\Repository\MenuNodeRepositoryInterface;
use Vivo\PageBundle\Seo\ActivePage;
use Vivo\SiteBundle\Seo\PageInterface;

class NavigationExtension extends \Twig_Extension
{
    /**
     * @var ActivePage
     */
    private $activePage;

    /**
     * @var \Vivo\SiteBundle\Seo\PageInterface
     */
    private $seoPage;

    /**
     * @var \Vivo\PageBundle\Repository\MenuNodeRepositoryInterface
     */
    private $menuNodeRepository;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ActivePage                  $activePage
     * @param PageInterface               $seoPage
     * @param MenuNodeRepositoryInterface $menuNodeRepository
     * @param Cache                       $cache
     * @param \Twig_Environment           $twig
     * @param RouterInterface             $router
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(
        ActivePage $activePage,
        PageInterface $seoPage,
        MenuNodeRepositoryInterface $menuNodeRepository,
        Cache $cache,
        \Twig_Environment $twig,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->activePage = $activePage;
        $this->seoPage = $seoPage;
        $this->menuNodeRepository = $menuNodeRepository;
        $this->cache = $cache;
        $this->twig = $twig;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_menu', array($this, 'renderMenu'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('render_sub_menu', array($this, 'renderSubMenu'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('render_breadcrumbs', array($this, 'renderBreadcrumbs'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Render menu.
     *
     * @param string $menuAlias
     * @param int    $maxLevel
     * @param string $template
     *
     * @return string
     */
    public function renderMenu($menuAlias, $maxLevel = 1, $template = '@VivoPage/Extension/Navigation/menu.html.twig')
    {
        $activePage = $this->activePage->getPage();

        $cacheId = hash('sha256', ($activePage ? $activePage->getId() : '').__FUNCTION__.serialize(func_get_args())).$this->seoPage->getSite()->getId();
        if ($this->cache->contains($cacheId)) {
            return $this->cache->fetch($cacheId);
        }

        $response = '';
        $menu = $this->menuNodeRepository->findOneActiveMenuByAlias($menuAlias, true);

        if ($menu) {
            $qb = $this->menuNodeRepository->getActiveMenuNodesQueryBuilder();
            $children = $this->menuNodeRepository->findChildren($menu, $maxLevel, $qb);
            $activeNodes = array();

            if ($activePage) {
                foreach ($activePage->getMenuNodes(false) as $node) {
                    $parents = $this->menuNodeRepository->findParentsOf($node);

                    $activeNodes[] = $node;
                    foreach ($parents as $parent) {
                        $activeNodes[] = $parent;
                    }
                }
            }

            $item = new Item($menu);

            $this->addChildren($item, $menu, $activeNodes, $maxLevel);

            $this->eventDispatcher->dispatch(Events::PRE_RENDER, new ItemEvent($item));

            $response = $this->twig->render($template, array(
                'menu' => $menu,
                'item' => $item,
                'max_level' => $maxLevel,
                'active_page' => $activePage,
            ));
        }

        $cacheTtl = rand(3600, 4500); // Randomly calculate ttl to try and avoid multiple menus generating at the same time
        $this->cache->save($cacheId, $response, $cacheTtl);

        return $response;
    }

    /**
     * Render sub menu.
     *
     * @param string $menuAlias
     * @param int    $maxLevel
     * @param string $template
     *
     * @return string
     */
    public function renderSubMenu($menuAlias, $maxLevel = 1, $template = '@VivoPage/Extension/Navigation/menu_sub.html.twig')
    {
        if (!$activePage = $this->activePage->getPage()) {
            return '';
        }

        if ($maxLevel < 1) {
            throw new \Exception('Max level must be greater than zero.');
        }

        $cacheId = hash('sha256', $activePage->getId().__FUNCTION__.serialize(func_get_args())).$this->seoPage->getSite()->getId();
        if ($this->cache->contains($cacheId)) {
            return $this->cache->fetch($cacheId);
        }

        $response = '';
        $menu = $this->menuNodeRepository->findOneActiveMenuByAlias($menuAlias);

        if ($menu) {
            $qb = $this->menuNodeRepository->getActiveMenuNodesQueryBuilder();
            $flatChildren = $this->menuNodeRepository->getFlatArrayChildren($menu, null, $qb);

            $childLevel = null;
            foreach ($flatChildren as $child) {
                /** @var \Vivo\PageBundle\Model\MenuNodeInterface $model */
                $model = $child->getModel();

                if ($model->getPage() && $model->getPage()->getId() === $activePage->getId()) {
                    $childLevel = $child;

                    break;
                }
            }

            if ($childLevel) {
                /** @var \Vivo\PageBundle\Model\MenuNodeInterface $activeNode */
                $activeNode = $childLevel->getModel();

                if (count($activeNode->getActiveChildren()) < 1) {
                    for ($i = 0; $i < $childLevel->getLevel(); ++$i) {
                        $activeNode = $activeNode->getParent();
                    }
                } else {
                    if ($childLevel->getLevel() > 0) {
                        $activeNode = $activeNode->getParent();
                    }
                }

                $activeNodes = array();
                foreach ($activePage->getMenuNodes(false) as $node) {
                    $parents = $this->menuNodeRepository->findParentsOf($node);

                    $activeNodes[] = $node;
                    foreach ($parents as $parent) {
                        $activeNodes[] = $parent;
                    }
                }

                $item = new Item($activeNode);
                $this->addChildren($item, $activeNode, $activeNodes, $maxLevel);

                $this->eventDispatcher->dispatch(Events::PRE_RENDER, new ItemEvent($item));

                $response = $this->twig->render($template, array(
                    'menu' => $activeNode,
                    'item' => $item,
                    'max_level' => $maxLevel,
                    'active_page' => $activePage,
                ));
            }
        }

        $cacheTtl = rand(3600, 4500); // Randomly calculate ttl to try and avoid multiple menus generating at the same time
        $this->cache->save($cacheId, $response, $cacheTtl);

        return $response;
    }

    /**
     * Render breadcrumb.
     *
     * @param bool   $include_homepage
     * @param string $separator
     * @param null   $intro
     * @param string $template
     *
     * @return string
     */
    public function renderBreadcrumbs($include_homepage = false, $separator = '/', $intro = null, $template = '@VivoPage/Extension/Navigation/breadcrumb.html.twig')
    {
        $breadcrumbs = $this->seoPage->getBreadcrumbs();

        if ($include_homepage && count($breadcrumbs) > 0) {
            array_unshift($breadcrumbs, array('Home', 'homepage', array()));
        }

        $parameters = array(
            'breadcrumbs' => $breadcrumbs,
            'separator' => $separator,
        );

        if (null !== $intro) {
            $parameters['intro'] = $intro;
        }

        return $this->twig->render($template, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_page_navigation_extension';
    }

    /**
     * @param ItemInterface     $parentItem
     * @param MenuNodeInterface $menuNode
     * @param array             $activeMenuNodes
     * @param int               $maxLevel
     */
    protected function addChildren(ItemInterface &$parentItem, MenuNodeInterface $menuNode, array $activeMenuNodes, $maxLevel)
    {
        $currentLevel = 0;
        $parent = $parentItem;

        do {
            ++$currentLevel;
        } while (null !== $parent = $parent->getParent());

        if ($currentLevel > $maxLevel) {
            $this->eventDispatcher->dispatch(Events::PRE_SET_CHILDREN, new ItemEvent($parentItem));
            $this->eventDispatcher->dispatch(Events::POST_SET_CHILDREN, new ItemEvent($parentItem));

            return;
        }

        $this->eventDispatcher->dispatch(Events::PRE_SET_CHILDREN, new ItemEvent($parentItem));

        foreach ($menuNode->getActiveChildren() as $child) {
            if ($child->getRouteName()) {
                $href = $this->router->generate($child->getRouteName());

                $item = new Item($child);

                $item->setParent($item)
                    ->setHref($href)
                    ->setName($child->getTitle())
                ;

                if (in_array($child, $activeMenuNodes, true)) {
                    $item->setActive(true);
                }

                $parentItem->addChild($item);

                $this->addChildren($item, $child, $activeMenuNodes, $maxLevel);
            }
        }

        $this->eventDispatcher->dispatch(Events::POST_SET_CHILDREN, new ItemEvent($parentItem));
    }
}
