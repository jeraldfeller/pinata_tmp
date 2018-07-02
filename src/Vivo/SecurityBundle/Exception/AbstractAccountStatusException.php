<?php

namespace Vivo\SecurityBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

abstract class AbstractAccountStatusException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Too many failed logins. Reset your password or try again later.';
    }
}
