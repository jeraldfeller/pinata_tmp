<?php

namespace Vivo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('latitude', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
            ->add('longitude', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
            ->add('zoom', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => false,
            'data_class' => 'Vivo\AddressBundle\Model\Point',
            'validation_groups' => array('Point'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_address_point';
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
