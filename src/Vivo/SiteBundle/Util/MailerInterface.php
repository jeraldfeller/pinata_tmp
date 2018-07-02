<?php

namespace Vivo\SiteBundle\Util;

use Vivo\SiteBundle\Model\SiteInterface;

interface MailerInterface
{
    /**
     * Create swift message with default values.
     *
     * @param SiteInterface $site
     * @param string        $templateName
     * @param array         $templateParams
     * @param array         $to
     * @param array         $from
     * @param array         $replyTo
     *
     * @return \Swift_Message
     */
    public function createMessage(SiteInterface $site, $templateName, array $templateParams = array(), array $to, array $from = null, array $replyTo = null);

    /**
     * Send message through site mailer.
     *
     * @param SiteInterface  $site
     * @param \Swift_Message $message
     *
     * @return int
     */
    public function send(SiteInterface $site, \Swift_Message $message);

    /**
     * Create swift message and send.
     *
     * @param SiteInterface $site
     * @param string        $templateName
     * @param array         $templateParams
     * @param array         $to
     * @param array         $from
     * @param array         $replyTo
     *
     * @return int
     */
    public function sendMessage(SiteInterface $site, $templateName, array $templateParams = array(), array $to, array $from = null, array $replyTo = null);

    /**
     * Add mailer.
     *
     * @param string        $mailerId
     * @param string        $mailerName
     * @param \Swift_Mailer $mailer
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function addMailer($mailerId, $mailerName, \Swift_Mailer $mailer);

    /**
     * Get mailers.
     *
     * @return \Swift_Mailer[]
     */
    public function getMailers();

    /**
     * Get the mailer for a site.
     *
     * @param SiteInterface $site
     *
     * @return \Swift_Mailer
     */
    public function getMailerForSite(SiteInterface $site);
}
