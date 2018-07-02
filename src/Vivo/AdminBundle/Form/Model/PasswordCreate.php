<?php

namespace Vivo\AdminBundle\Form\Model;

use Vivo\AdminBundle\Model\UserInterface;
use Vivo\UtilBundle\Util\PasswordGenerator;

class PasswordCreate
{
    /**
     * @var UserInterface
     */
    public $user;

    /**
     * @var string
     */
    public $newPassword;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->newPassword = PasswordGenerator::generatePassword();
        $this->user = $user;
    }
}
