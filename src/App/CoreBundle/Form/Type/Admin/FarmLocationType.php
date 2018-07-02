<?php

namespace App\CoreBundle\Form\Type\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FarmLocationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address', 'vivo_address_map', array(
                'inherit_data' => true,
            ))
            ->add('disabledAt', 'vivo_util_checkbox_datetime', array(
                'inverted' => true,
                'label' => 'Active',
                'required' => false,
                'help_inline' => 'Tick to enable farm location',
            ))
            ->add('farms', 'entity', array(
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'empty_value' => 'None',
                'class' => 'AppCoreBundle:Farm',
                'property' => 'name',
                'label' => 'Farms',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('b')
                            ->orderBy('b.name', 'ASC')
                            ->addOrderBy('b.id', 'ASC');
                    },
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CoreBundle\Entity\FarmLocation',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_corebundle_farmlocation';
    }
}
