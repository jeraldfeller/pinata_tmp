<?php

namespace App\CoreBundle\Menu;

use Knp\Menu\ItemInterface;
use Vivo\AdminBundle\Menu\MainMenuBuilder as BaseMenu;

class MainMenuBuilder extends BaseMenu
{
    /**
     * {@inheritdoc}
     */
    protected function addBodyNav(ItemInterface $menu)
    {
        if ($this->isGranted('ROLE_PAGE_MANAGEMENT') || $this->isGranted('ROLE_NAVIGATION_MANAGEMENT') || $this->isGranted('ROLE_BLOG_MANAGEMENT')) {
            /** @var ItemInterface $contentMenu */
            $contentMenu = $this->createDropdownMenuItem($menu, 'Content', false, array('caret' => true));

            if ($this->isGranted('ROLE_PAGE_MANAGEMENT') && $this->isGranted('ROLE_NAVIGATION_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('Pages & Navigation', array('route' => 'vivo_page.admin.tree.index'));
            } elseif ($this->isGranted('ROLE_NAVIGATION_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('Navigation', array('route' => 'vivo_page.admin.menu.index'));
            } else {
                $dropdown = $contentMenu->addChild('Pages', array('route' => 'vivo_page.admin.tree.index'));
            }

            $dropdown->setAttribute('class', 'dropdown-submenu');
            $dropdown->setChildrenAttribute('class', 'dropdown-menu');

            $this->currentDropdownRoutePattern($dropdown, array(
                '^vivo_page.',
            ));

            if ($this->isGranted('ROLE_PAGE_MANAGEMENT')) {
                $dropdown->addChild('Pages', array('route' => 'vivo_page.admin.tree.index'));
                $dropdown->addChild('Create Page', array('route' => 'vivo_page.admin.page.create'));
            }

            if ($this->isGranted('ROLE_NAVIGATION_MANAGEMENT')) {
                if ($this->isGranted('ROLE_PAGE_MANAGEMENT')) {
                    $this->addDivider($dropdown);
                }

                $dropdown->addChild('Navigation', array('route' => 'vivo_page.admin.menu.index'));
                $dropdown->addChild('Create Menu', array('route' => 'vivo_page.admin.menu.create'));
            }

            if ($this->isGranted('ROLE_PAGE_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('FAQs', array('route' => 'app_faq.admin.faq.index'));
                $dropdown->setAttribute('class', 'dropdown-submenu');
                $dropdown->setChildrenAttribute('class', 'dropdown-menu');

                $dropdown->addChild('FAQs', array('route' => 'app_faq.admin.faq.index'));
                $dropdown->addChild('Create FAQ', array('route' => 'app_faq.admin.faq.create'));

                $this->addDivider($dropdown);
                $dropdown->addChild('Categories', array('route' => 'app_faq.admin.category.index'));
                $dropdown->addChild('Create Category', array('route' => 'app_faq.admin.category.create'));
            }

            if ($this->isGranted('ROLE_HISTORY_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('History', array('route' => 'app_core.admin.timeline.index'));
                $dropdown->setAttribute('class', 'dropdown-submenu');
                $dropdown->setChildrenAttribute('class', 'dropdown-menu');

                $this->currentDropdownRoutePattern($dropdown, array(
                    '^app_core.admin.timeline',
                ));

                $dropdown->addChild('History', array('route' => 'app_core.admin.timeline.index'));
                $dropdown->addChild('Create Timeline Item', array('route' => 'app_core.admin.timeline.create'));
            }

            if ($this->isGranted('ROLE_PAGE_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('Promo Blocks', array('route' => 'app_core.admin.promo_block.index'));
                $dropdown->setAttribute('class', 'dropdown-submenu');
                $dropdown->setChildrenAttribute('class', 'dropdown-menu');

                $this->currentDropdownRoutePattern($dropdown, array(
                    '^app_core.admin.promo_block',
                ));

                $dropdown->addChild('Promo Blocks', array('route' => 'app_core.admin.promo_block.index'));
                $dropdown->addChild('Create Promo Block', array('route' => 'app_core.admin.promo_block.create'));

                $dropdown->addChild('Promo Groups', array('route' => 'app_core.admin.promo_block_group.index'));
                $dropdown->addChild('Create Group', array('route' => 'app_core.admin.promo_block_group.create'));
            }

            if ($this->isGranted('ROLE_FARM_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('Farms', array('route' => 'app_core.admin.farm.index'));
                $dropdown->setAttribute('class', 'dropdown-submenu');
                $dropdown->setChildrenAttribute('class', 'dropdown-menu');

                $this->currentDropdownRoutePattern($dropdown, array(
                    '^app_core.admin.farm',
                ));

                $dropdown->addChild('Farms', array('route' => 'app_core.admin.farm.index'));
                $dropdown->addChild('Create Farm', array('route' => 'app_core.admin.farm.create'));

                $dropdown->addChild('Farm Locations', array('route' => 'app_core.admin.farm_location.index'));
                $dropdown->addChild('Create Farm Locations', array('route' => 'app_core.admin.farm_location.create'));
            }

            if ($this->isGranted('ROLE_FRUIT_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('Fruits', array('route' => 'app_core.admin.fruit.index'));
                $dropdown->setAttribute('class', 'dropdown-submenu');
                $dropdown->setChildrenAttribute('class', 'dropdown-menu');

                $this->currentDropdownRoutePattern($dropdown, array(
                    '^app_core.admin.fruit',
                ));

                $dropdown->addChild('Fruits', array('route' => 'app_core.admin.fruit.index'));
                $dropdown->addChild('Create Fruit', array('route' => 'app_core.admin.fruit.create'));
            }

            if ($this->isGranted('ROLE_TEAM_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('Team Profiles', array('route' => 'app_team.admin.profile.index'));
                $dropdown->setAttribute('class', 'dropdown-submenu');
                $dropdown->setChildrenAttribute('class', 'dropdown-menu');

                $this->currentDropdownRoutePattern($dropdown, array(
                    '^app_team.',
                ));

                $dropdown->addChild('Profiles', array('route' => 'app_team.admin.profile.index'));
                $dropdown->addChild('Create Profile', array('route' => 'app_team.admin.profile.create'));
            }

            if ($this->isGranted('ROLE_BLOG_MANAGEMENT')) {
                $dropdown = $contentMenu->addChild('Chatter', array('route' => 'vivo_blog.admin.post.index'));
                $dropdown->setAttribute('class', 'dropdown-submenu');
                $dropdown->setChildrenAttribute('class', 'dropdown-menu');

                $this->currentDropdownRoutePattern($dropdown, array(
                    '^vivo_blog.',
                ));

                $dropdown->addChild('Posts', array('route' => 'vivo_blog.admin.post.index'));
                $dropdown->addChild('Create Post', array('route' => 'vivo_blog.admin.post.create'));

                $this->addDivider($dropdown);
                $dropdown->addChild('Categories', array('route' => 'vivo_blog.admin.category.index'));
                $dropdown->addChild('Create Category', array('route' => 'vivo_blog.admin.category.create'));
            }
        }

        return $menu;
    }
}
