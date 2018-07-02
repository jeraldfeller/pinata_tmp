<?php

namespace Vivo\AdminBundle\Form\Model;

use Vivo\AdminBundle\Model\UserInterface;

class PasswordChange
{
    /**
     * @var UserInterface
     */
    public $user;

    /**
     * @var bool
     */
    public $changePassword;

    /**
     * @var string
     */
    public $currentPassword;

    /**
     * @var string
     */
    public $newPassword;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
        $this->changePassword = false;
    }

    /**
     * @return bool
     */
    public function isPasswordDifferent()
    {
        return null !== $this->newPassword && $this->currentPassword == $this->newPassword;
    }
}
