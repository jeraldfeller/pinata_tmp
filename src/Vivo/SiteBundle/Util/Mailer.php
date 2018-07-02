<?php

namespace Vivo\SiteBundle\Util;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Vivo\SiteBundle\Model\SiteInterface;

class Mailer implements MailerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var \Swift_Mailer
     */
    protected $defaultMailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Swift_Mailer[]
     */
    protected $mailers;

    /**
     * @var SiteTemplateFinderInterface
     */
    protected $siteTemplateFinder;

    /**
     * Constructor.
     *
     * @param RouterInterface             $router
     * @param \Swift_Mailer               $defaultMailer
     * @param \Twig_Environment           $twig
     * @param FileLocatorInterface        $locator
     * @param TemplateNameParserInterface $parser
     * @param string                      $kernelRootDir
     * @param array                       $bundles
     */
    public function __construct(
        RouterInterface $router,
        \Swift_Mailer $defaultMailer,
        \Twig_Environment $twig,
        SiteTemplateFinderInterface $siteTemplateFinder
    ) {
        $this->router = $router;
        $this->defaultMailer = $defaultMailer;
        $this->twig = $twig;
        $this->siteTemplateFinder = $siteTemplateFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(SiteInterface $site, $templateName, array $templateParams = array(), array $to, array $from = null, array $replyTo = null)
    {
        if (null === $from || (is_array($from) && count($from) < 1)) {
            $from = array(
                $site->getSenderEmail() => $site->getSenderName(),
            );
        }

        $originalRouterContext = clone $this->router->getContext();

        $primaryDomain = $site->getPrimaryDomain();
        $routerContext = $this->router->getContext();
        $routerContext->setScheme($primaryDomain->getScheme());
        $routerContext->setHost($primaryDomain->getHost(true));

        $template = $this->siteTemplateFinder->loadTemplate($site, $templateName);
        $subject = $this->renderBlock($template, 'template_subject', $templateParams);
        $bodyText = $this->renderBlock($template, 'template_body_text', $templateParams);
        $bodyHtml = $this->renderBlock($template, 'template_body_html', $templateParams);

        $message = \Swift_Message::newInstance();

        $message->setSubject($subject)
            ->setFrom($from)
            ->setTo($to);

        if (null !== $replyTo) {
            $message->setReplyTo($replyTo);
        }

        if (empty($bodyHtml)) {
            // No html - Send plain text only
            $message->setBody($bodyText, 'text/plain');
        } else {
            $message->setBody($bodyHtml, 'text/html');

            if (!empty($bodyText)) {
                $message->addPart($bodyText, 'text/plain');
            }
        }

        $this->router->setContext($originalRouterContext);

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function send(SiteInterface $site, \Swift_Message $message)
    {
        return $this->getMailerForSite($site)
            ->send($message);
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage(SiteInterface $site, $templateName, array $templateParams = array(), array $to, array $from = null, array $replyTo = null)
    {
        $message = $this->createMessage($site, $templateName, $templateParams, $to, $from, $replyTo);

        return $this->send($site, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addMailer($mailerId, $mailerName, \Swift_Mailer $mailer)
    {
        if (isset($this->mailers[$mailerId])) {
            throw new \Exception(sprintf("Mailer with id '%s' is already set.", $mailerId));
        }

        $this->mailers[$mailerId] = array(
            'name' => $mailerName,
            'mailer' => $mailer,
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailers()
    {
        return $this->mailers;
    }

    /**
     * {@inheritdoc}
     */
    public function getMailerForSite(SiteInterface $site)
    {
        if (null !== $site->getMailer()) {
            if (isset($this->mailers[$site->getMailer()])) {
                return $this->mailers[$site->getMailer()]['mailer'];
            }
        }

        return $this->defaultMailer;
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->twig;
    }

    /**
     * Return html for specific block in a template.
     *
     * @param \Twig_Template $template
     * @param string         $block
     * @param array          $context
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function renderBlock(\Twig_Template $template, $block, array $context)
    {
        $context = $this->getTwig()->mergeGlobals($context);
        $level = ob_get_level();
        ob_start();
        try {
            $rendered = $template->renderBlock($block, $context);
            ob_end_clean();

            return $rendered;
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }
}
