<?php

namespace App\CoreBundle\Form\Extension;

use App\CoreBundle\Model\Choice\SlideColorClassChoice;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AssetBackgroundImageExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['include_background_colour_picker']) {
            $builder
                ->add('colorClass', 'choice', array(
                    'choices' => SlideColorClassChoice::$choices,
                ))
            ;
        }

        if ($options['include_subtitle']) {
            $builder
                ->add('subtitle', 'textarea', array(
                    'required' => false,
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'include_background_colour_picker' => false,
            'include_subtitle' => false,
        ));

        $resolver->setAllowedTypes(array(
            'include_background_colour_picker' => 'bool',
            'include_subtitle' => 'bool',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'vivo_asset_asset_image';
    }
}
