<?php

namespace Vivo\AdminBundle\Form\Model;

use Vivo\AdminBundle\Model\UserInterface;

class PasswordUpdate extends PasswordCreate
{
    /**
     * @var bool
     */
    public $updatePassword;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        parent::__construct($user);

        $this->updatePassword = false;
    }
}
