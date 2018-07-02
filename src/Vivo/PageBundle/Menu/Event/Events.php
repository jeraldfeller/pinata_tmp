<?php

namespace Vivo\PageBundle\Menu\Event;

final class Events
{
    /**
     * Fired before children are added to ItemInterface.
     *
     * The event object is ItemEvent.
     */
    const PRE_SET_CHILDREN = 'vivo_page.menu.pre_set_children';

    /**
     * Fired after children are added to ItemInterface.
     *
     * The event object is ItemEvent.
     */
    const POST_SET_CHILDREN = 'vivo_page.menu.post_set_children';

    /**
     * Fired just before the menu is rendered.
     *
     * The event object is ItemEvent.
     */
    const PRE_RENDER = 'vivo_page.menu.pre_render';

    private function __construct()
    {
    }
}
