<?php

namespace Vivo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Vivo\AddressBundle\Model\AddressInterface;
use Vivo\AddressBundle\Model\ExtendedAddressInterface;

class MapType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('latLng', 'Vivo\AddressBundle\Form\Type\PointType', array_merge(
            array(
                'constraints' => array(
                    new Valid(),
                ),
            ),
            $options['point_options']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'point_options' => array(),
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if ($data instanceof ExtendedAddressInterface) {
                    if (true === $data->isIgnored()) {
                        return false;
                    }
                }

                if ($data instanceof AddressInterface) {
                    return array('DefaultAddress');
                }

                return;
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Vivo\AddressBundle\Form\Type\AddressType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_address_map';
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
