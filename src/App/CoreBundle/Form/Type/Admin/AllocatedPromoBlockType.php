<?php

namespace App\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllocatedPromoBlockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rank', 'hidden', array(
                'empty_data' => 9999,
                'error_bubbling' => false,
            ))
            ->add('block', 'entity', array(
                'error_bubbling' => true,
                'property' => 'name',
                'empty_data' => '',
                'class' => 'App\CoreBundle\Entity\PromoBlock',
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CoreBundle\Entity\AllocatedPromoBlock',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_corebundle_allocated_promo_block';
    }
}
