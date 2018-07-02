<?php

namespace Vivo\AdminBundle\Util;

use Vivo\AdminBundle\Model\UserInterface;

interface MailerInterface
{
    /**
     * Send password reset request email to user.
     *
     * @param UserInterface $user
     *
     * @return int
     */
    public function sendPasswordResetRequestEmail(UserInterface $user);

    /**
     * Send user email notifying them that their password has changed.
     *
     * @param UserInterface $user
     * @param string        $plainPassword
     *
     * @return int
     */
    public function sendUserPasswordChangedEmail(UserInterface $user, $plainPassword);

    /**
     * Send user email notifying them of their new account.
     *
     * @param UserInterface $user
     * @param string        $plainPassword
     *
     * @return int
     */
    public function sendUserCreateEmail(UserInterface $user, $plainPassword);
}
