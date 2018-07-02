<?php

namespace App\CoreBundle\Form\Type\Admin;

use App\CoreBundle\Model\Choice\IconChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimelineType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('date')
            ->add('icon', 'choice', array(
                'required' => true,
                'choices' => IconChoice::$icons,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CoreBundle\Entity\Timeline',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_corebundle_timelineitem';
    }
}
