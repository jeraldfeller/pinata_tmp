<?php

namespace Vivo\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Repository\SiteRepositoryInterface;

class MainMenuBuilder extends AbstractNavbarMenuBuilder
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var TokenStorageInterface
     */
    private $authTokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var SiteFactory
     */
    private $siteFactory;

    /**
     * @param FactoryInterface              $factory
     * @param RequestStack                  $requestStack
     * @param TokenStorageInterface         $authTokenStorage
     * @param AuthorizationCheckerInterface $authChecker
     * @param SiteFactory                   $siteFactory
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack, TokenStorageInterface $authTokenStorage, AuthorizationCheckerInterface $authChecker, SiteFactory $siteFactory)
    {
        parent::__construct($factory);

        $this->requestStack = $requestStack;
        $this->authTokenStorage = $authTokenStorage;
        $this->authChecker = $authChecker;
        $this->siteFactory = $siteFactory;
    }

    /**
     * Main Menu.
     *
     * @return ItemInterface
     */
    public function createMainMenu()
    {
        $menu = $this->createNavbarMenuItem();

        $this->addHomepage($menu);

        if ($this->getUser()) {
            $this->addBodyNav($menu);
            $this->addUserGroupNav($menu);
            $this->addSiteNav($menu);
        }

        return $menu;
    }

    /**
     * @param SiteRepositoryInterface $siteRepository
     *
     * @return ItemInterface
     */
    public function createSiteSwitchMenu(SiteRepositoryInterface $siteRepository)
    {
        $sites = $siteRepository->findAllWithDomains();

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav pull-right');

        if (!$user = $this->getUser()) {
            return $menu;
        }

        if (count($sites) > 1) {
            $dropdown = $this->createDropdownMenuItem($menu, $this->getSite()->getName(), true, array('caret' => true));

            foreach ($sites as $site) {
                $item = $dropdown->addChild($site->getName(), array(
                    'route' => 'vivo_site.admin.site.switch',
                    'routeParameters' => array(
                        'id' => $site->getId(),
                        'route' => $this->getRequest()->attributes->get('_route'),
                        'route_params' => array_merge(
                            $this->getRequest()->query->all(),
                            $this->getRequest()->attributes->get('_route_params')
                        ),
                    ),
                ));
            }

            $accountText = $user->getFirstName().' '.$user->getLastName();
        } else {
            $accountText = 'Logged in as '.$user->getFirstName().' '.$user->getLastName();
        }

        $dropdown = $this->createDropdownMenuItem($menu, $accountText, true, array('caret' => true));
        $dropdown->addChild('My Profile', array('route' => 'vivo_admin.profile.edit'));
        $dropdown->addChild('Logout', array('route' => 'vivo_admin.security.logout'));

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     *
     * @return $this
     */
    protected function addHomepage(ItemInterface $menu)
    {
        $menu->addChild('Dashboard', array('route' => 'admin_homepage'));

        return $this;
    }

    /**
     * @param ItemInterface $menu
     *
     * @return $this
     */
    protected function addBodyNav(ItemInterface $menu)
    {
        return $this;
    }

    /**
     * @param ItemInterface $menu
     *
     * @return $this
     */
    protected function addUserGroupNav(ItemInterface $menu)
    {
        if (!$this->getUser()) {
            return $this;
        }

        $dropdown = null;
        if ($this->isGranted('ROLE_USER_GROUP_MANAGEMENT') && $this->isGranted('ROLE_USER_MANAGEMENT')) {
            $dropdown = $this->createDropdownMenuItem($menu, 'Users & Groups', false, array('caret' => true));
        } elseif ($this->isGranted('ROLE_USER_GROUP_MANAGEMENT')) {
            $dropdown = $this->createDropdownMenuItem($menu, 'User Groups', false, array('caret' => true));
        } elseif ($this->isGranted('ROLE_USER_MANAGEMENT')) {
            $dropdown = $this->createDropdownMenuItem($menu, 'Users', false, array('caret' => true));
        }

        if ($dropdown) {
            $this->currentDropdownRoutePattern($dropdown, array(
                '^vivo_admin.user.',
                '^vivo_admin.group.',
            ));

            if ($this->isGranted('ROLE_USER_MANAGEMENT')) {
                $dropdown->addChild('Users', array('route' => 'vivo_admin.user.index'));
                $dropdown->addChild('Create User', array('route' => 'vivo_admin.user.create'));
            }

            if ($this->isGranted('ROLE_USER_GROUP_MANAGEMENT')) {
                if ($this->isGranted('ROLE_USER_MANAGEMENT')) {
                    $this->addDivider($dropdown);
                }

                $dropdown->addChild('User Groups', array('route' => 'vivo_admin.group.index'));
                $dropdown->addChild('Create User Group', array('route' => 'vivo_admin.group.create'));
            }
        }

        return $this;
    }

    /**
     * @param ItemInterface $menu
     *
     * @return $this
     */
    protected function addSiteNav(ItemInterface $menu)
    {
        if (!$this->getUser()) {
            return $this;
        }

        if ($this->isGranted('ROLE_DEVELOPER')) {
            $dropdown = $this->createDropdownMenuItem($menu, 'Sites', false, array('caret' => true));

            $this->currentDropdownRoutePattern($dropdown, array(
                '^vivo_site.admin.site.',
            ));

            $dropdown->addChild('Sites', array('route' => 'vivo_site.admin.site.index'));
            $dropdown->addChild('Create Site', array('route' => 'vivo_site.admin.site.create'));
        } elseif ($this->isGranted('ROLE_SITE_MANAGEMENT')) {
            $menu->addChild('Site Settings', array('route' => 'vivo_site.admin.site.edit'));
        }

        return $this;
    }

    /**
     * @param ItemInterface $menu
     *
     * @return $this
     */
    protected function appendExtraNav(ItemInterface $menu)
    {
        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface|\Vivo\AdminBundle\Model\UserInterface|null
     */
    protected function getUser()
    {
        if (null === $token = $this->authTokenStorage->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }

    /**
     * @param $role
     *
     * @return bool
     */
    protected function isGranted($role)
    {
        return $this->authChecker->isGranted($role);
    }

    /**
     * Get site.
     *
     * @return null|SiteInterface
     */
    protected function getSite()
    {
        return $this->siteFactory->get();
    }

    /**
     * @param ItemInterface $item
     * @param array         $patterns
     */
    protected function currentDropdownRoutePattern(ItemInterface $item, array $patterns)
    {
        $route = $this->getRequest()->attributes->get('_route');

        if (preg_match('/'.implode('|', $patterns).'/', $route)) {
            $item->setCurrent(true);
        }
    }
}
