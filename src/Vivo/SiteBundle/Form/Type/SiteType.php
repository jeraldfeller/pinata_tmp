<?php

namespace Vivo\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\SiteBundle\Util\MailerInterface;

class SiteType extends AbstractType
{
    /**
     * @var string
     */
    protected $siteClass;

    /**
     * @var string
     */
    protected $domainClass;

    /**
     * @var bool
     */
    protected $enableGoogle;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * SiteType constructor.
     *
     * @param string          $siteClass
     * @param string          $domainClass
     * @param bool            $enableGoogle
     * @param MailerInterface $mailer
     */
    public function __construct($siteClass, $domainClass, $enableGoogle, MailerInterface $mailer)
    {
        $this->siteClass = $siteClass;
        $this->domainClass = $domainClass;
        $this->enableGoogle = $enableGoogle;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'admin.form.site.name',
            ))
            ->add('shortName', null, array(
                'label' => 'admin.form.site.shortName.label',
                'help_label_tooltip' => array(
                    'title' => 'admin.form.site.shortName.help',
                ),
            ))
            ->add('senderName', null, array(
                'label' => 'admin.form.site.senderName',
            ))
            ->add('senderEmail', null, array(
                'label' => 'admin.form.site.senderEmail',
            ))
            ->add('notificationEmail', null, array(
                'label' => 'admin.form.site.notificationEmail',
            ))
            ->add('berryWorldEmail', null, array(
                'label' => 'admin.form.site.berryWorldEmail',
            ))
            ->add('berryWorldWebsite', null, array(
                'label' => 'admin.form.site.berryWorldWebsite',
            ))
        ;

        if ($this->enableGoogle) {
            $builder
                ->add('googleAnalyticsId', null, array(
                    'label' => 'admin.form.site.googleAnalyticsId',
                    ))
                ->add('googleAdvertiserSupport', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                    'required' => false,
                    'label' => 'admin.form.site.googleAdvertiserSupport',
                ))
                ->add('googleSiteVerificationCode', null, array(
                    'label' => 'admin.form.site.googleSiteVerificationCode',
                ))
            ;
        }

        if (true === $options['is_developer']) {
            $builder
                ->add('status', 'Vivo\SiteBundle\Form\Choice\StatusChoice', array(
                    'label' => 'admin.form.site.status',
                ))
                ->add('primary', null, array(
                    'label' => 'admin.form.site.primary',
                ))
                ->add('theme', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'required' => false,
                    'label' => 'admin.form.site.theme',
                ))
                ->add('domains', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                    'label' => 'admin.form.site.host',
                    'entry_type' => 'Vivo\SiteBundle\Form\Type\DomainType',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ))
            ;

            if (count($this->mailer->getMailers()) > 0) {
                $mailers = array();
                foreach ($this->mailer->getMailers() as $mailerId => $mailer) {
                    $mailers[$mailerId] = $mailer['name'];
                }

                $builder->add('mailer', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                    'required' => false,
                    'label' => 'admin.form.site.mailer',
                    'choices' => $mailers,
                    'placeholder' => 'Default',
                ));
            }

            if ($this->enableGoogle) {
                $builder
                    ->add('googleApiServerKey', null, array(
                        'label' => 'admin.form.site.googleApiServerKey',
                    ))
                    ->add('googleApiBrowserKey', null, array(
                        'label' => 'admin.form.site.googleApiBrowserKey',
                    ))
                ;
            }

            $domainClass = $this->domainClass;

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($domainClass) {
                $data = $event->getData();

                if (null === $data) {
                    return;
                }

                if (count($data->getDomains()) < 1) {
                    $reflection = new \ReflectionClass($domainClass);

                    $domain = $reflection->newInstance();
                    $domain->setPrimary(true);
                    $data->addDomain($domain);
                    $event->setData($data);
                }
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->siteClass,
            'translation_domain' => 'VivoSiteBundle',
            'is_developer' => false,
        ));

        $resolver->setAllowedTypes('is_developer', array('bool'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_site_site_type';
    }

    /**
     * BC - Add alias if Symfony < 3.0.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
