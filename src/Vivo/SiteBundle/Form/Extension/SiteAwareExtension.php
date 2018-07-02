<?php

namespace Vivo\SiteBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Vivo\SiteBundle\Doctrine\FilterAware\SiteAwareInterface;
use Vivo\SiteBundle\Factory\SiteFactory;

class SiteAwareExtension extends AbstractTypeExtension
{
    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @param SiteFactory $siteFactory
     */
    public function __construct(SiteFactory $siteFactory)
    {
        $this->siteFactory = $siteFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if ($data instanceof SiteAwareInterface) {
                if (null !== $data->getSite()) {
                    return;
                }

                if ($site = $this->siteFactory->get()) {
                    $data->setSite($site);
                    $event->setData($data);
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FormType';
    }
}
