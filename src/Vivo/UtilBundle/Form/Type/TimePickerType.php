<?php

namespace Vivo\UtilBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TimePickerType.
 */
class TimePickerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Vivo\UtilBundle\Form\Type\DateTimePickerType';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'pick_date' => false,
            'js_format' => 'hh:mm',
            'format' => 'HH:mm',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_util_time';
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
