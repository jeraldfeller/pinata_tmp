<?php

namespace Vivo\AdminBundle\Util;

use Vivo\AdminBundle\Model\UserInterface;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Util\MailerInterface as SiteMailerInterface;

class Mailer implements MailerInterface
{
    /**
     * @var SiteMailerInterface
     */
    protected $siteMailer;

    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @param SiteMailerInterface $siteMailer
     * @param SiteFactory         $siteFactory
     */
    public function __construct(SiteMailerInterface $siteMailer, SiteFactory $siteFactory)
    {
        $this->siteMailer = $siteMailer;
        $this->siteFactory = $siteFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function sendPasswordResetRequestEmail(UserInterface $user)
    {
        $site = $this->siteFactory->get();

        $recipients = array(
            $user->getEmail() => $user->getFirstName().' '.$user->getLastName(),
        );

        $context = array(
            'user' => $user,
            'site' => $site,
        );

        return $this->siteMailer->sendMessage($site, '@VivoAdmin/Mailer/password_reset.html.twig', $context, $recipients);
    }

    /**
     * {@inheritdoc}
     */
    public function sendUserPasswordChangedEmail(UserInterface $user, $plainPassword)
    {
        $site = $this->siteFactory->get();

        $to = array(
            $user->getEmail() => $user->getFirstName().' '.$user->getLastName(),
        );

        $context = array(
            'user' => $user,
            'site' => $site,
            'plain_password' => $plainPassword,
        );

        return $this->siteMailer->sendMessage($site, '@VivoAdmin/Mailer/password_changed.html.twig', $context, $to);
    }

    /**
     * {@inheritdoc}
     */
    public function sendUserCreateEmail(UserInterface $user, $plainPassword)
    {
        $site = $this->siteFactory->get();

        $to = array(
            $user->getEmail() => $user->getFirstName().' '.$user->getLastName(),
        );

        $context = array(
            'user' => $user,
            'site' => $site,
            'plain_password' => $plainPassword,
        );

        return $this->siteMailer->sendMessage($site, '@VivoAdmin/Mailer/account_created.html.twig', $context, $to);
    }
}
