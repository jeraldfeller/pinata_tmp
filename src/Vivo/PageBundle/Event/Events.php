<?php

namespace Vivo\PageBundle\Event;

final class Events
{
    /**
     * Fired when the seo meta should be prepared.
     *
     * The event object is Vivo\PageBundle\Event\PrepareSeoEvent
     */
    const PREPARE_SEO = 'vivo_page.seo.prepare';

    private function __construct()
    {
    }
}
