<?php

namespace App\SiteBundle\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;

class SiteExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteLogo', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'options' => array(
                    'data_class' => 'App\SiteBundle\Entity\SiteLogo',
                ),
            ))
            ->add('phone', null, array(
                'label' => 'Global Phone Number',
            ))
            ->add('fax', null, array(
                'label' => 'Global Fax Number',
            ))
            ->add('postOfficeBox', null, array(
                'label' => 'Global PO Box',
            ))
            ->add('contactEmail', null, array(
                'label' => 'Global Contact Email',
            ))
            ->add('address', null, array(
                'label' => 'Address',
            ))
            ->add('facebookURL', 'url', array(
                'label' => 'Facebook URL',
            ))
            ->add('youtubeURL', 'url', array(
                'label' => 'YouTube URL',
            ))
            ->add('instagramURL', 'url', array(
                'label' => 'Instagram URL',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'Vivo\SiteBundle\Form\Type\SiteType';
    }
}
