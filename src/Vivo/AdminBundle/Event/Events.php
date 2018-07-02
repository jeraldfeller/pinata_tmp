<?php

namespace Vivo\AdminBundle\Event;

final class Events
{
    /**
     * Fired after a password is changed.
     *
     * The event object is \Vivo\AdminBundle\Event\PasswordChangedEvent
     */
    const PASSWORD_CHANGED = 'vivo_admin.password_changed';

    private function __construct()
    {
    }
}
