<?php

namespace Vivo\UtilBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DateTimePickerType.
 *
 * See http://tarruda.github.io/bootstrap-datetimepicker/ for JS documentation
 */
class DateTimePickerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['pick_date'] = $options['pick_date'];
        $view->vars['pick_time'] = $options['pick_time'];
        $view->vars['pick_seconds'] = $options['pick_seconds'];
        $view->vars['js_format'] = $options['js_format'];
        $view->vars['view_mode'] = $options['view_mode'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\DateTimeType';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'pick_date' => true,
            'pick_time' => true,
            'pick_seconds' => false, // If this is set to true make sure you have a date format with seconds
            'js_format' => 'dd/MM/yyyy hh:mm',
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy HH:mm',
            'view_mode' => null,
        ));

        $resolver->setRequired(array(
            'pick_date', 'pick_time', 'pick_seconds', 'js_format',
        ));

        $resolver->setAllowedTypes('pick_date', array('bool'));
        $resolver->setAllowedTypes('pick_time', array('bool'));
        $resolver->setAllowedTypes('pick_seconds', array('bool'));
        $resolver->setAllowedTypes('js_format', array('string'));
        $resolver->setAllowedTypes('view_mode', array('null', 'integer'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_util_datetime';
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
