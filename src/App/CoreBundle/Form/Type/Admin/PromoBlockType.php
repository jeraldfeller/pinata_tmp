<?php

namespace App\CoreBundle\Form\Type\Admin;

use App\CoreBundle\Model\Choice\IconChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PromoBlockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'help_inline' => 'Not displayed - only for admin purposes',
            ))
            ->add('icon', 'choice', array(
                'placeholder' => '--Select an Icon--',
                'required' => false,
                'choices' => IconChoice::$icons,
            ))
            ->add('iconPosition', 'choice', array(
                'required' => false,
                'choices' => IconChoice::$positions,
            ))
            ->add('content', 'textarea', array(
                'help_inline' => 'Max Characters 255',
            ))
            ->add('url', 'url', array(
                'help_inline' => 'The URL this promo block will link to',
            ))
            ->add('newWindow', null, array(
                'help_inline' => 'Open link in a new window',
                'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CoreBundle\Entity\PromoBlock',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_corebundle_promoblock';
    }
}
