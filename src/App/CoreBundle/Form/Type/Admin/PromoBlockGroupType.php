<?php

namespace App\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PromoBlockGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('blocks', 'collection', array(
                'error_bubbling' => true,
                'type' => new AllocatedPromoBlockType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => true,
                'data_class' => null,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CoreBundle\Entity\PromoBlockGroup',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_corebundle_promoblockgroup';
    }
}
