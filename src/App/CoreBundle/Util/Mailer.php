<?php

namespace App\CoreBundle\Util;

use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SiteBundle\Model\Site;
use App\CoreBundle\Entity\Contact;
use Vivo\SiteBundle\Util\MailerInterface as SiteMailerInterface;

class Mailer
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
     * Send contact email to admin.
     *
     * @param Contact $contact
     *
     * @return int
     */
    public function sendContactEmail(Contact $contact)
    {
        $site = $this->siteFactory->get();

        if (!$site->getNotificationEmail()) {
            return 0;
        }

        $recipients = array(
            $site->getNotificationEmail() => null,
        );

        $replyTo = array(
            $contact->getEmail() => sprintf('%s', $contact->getName()),
        );

        $context = array(
            'contact' => $contact,
            'site' => $site,
        );

        return $this->siteMailer->sendMessage($site, '@AppCore/Mailer/contact.html.twig', $context, $recipients, null, $replyTo);
    }
}
