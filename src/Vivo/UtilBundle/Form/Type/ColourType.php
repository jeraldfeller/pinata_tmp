<?php

namespace Vivo\UtilBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColourType.
 *
 * Refer to http://labs.abeautifulsite.net/jquery-minicolors/ for documentation
 */
class ColourType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['animationSpeed'] = $options['animationSpeed'];
        $view->vars['animationEasing'] = $options['animationEasing'];
        $view->vars['change'] = $options['change'];
        $view->vars['changeDelay'] = $options['changeDelay'];
        $view->vars['control'] = $options['control'];
        $view->vars['dataUris'] = $options['dataUris'];
        $view->vars['defaultValue'] = $options['defaultValue'];
        $view->vars['hide'] = $options['hide'];
        $view->vars['hideSpeed'] = $options['hideSpeed'];
        $view->vars['inline'] = $options['inline'];
        $view->vars['letterCase'] = $options['letterCase'];
        $view->vars['opacity'] = $options['opacity'];
        $view->vars['position'] = $options['position'];
        $view->vars['show'] = $options['show'];
        $view->vars['showSpeed'] = $options['showSpeed'];
        $view->vars['theme'] = $options['theme'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'animationSpeed' => null,
            'animationEasing' => null,
            'change' => null,
            'changeDelay' => null,
            'control' => null,
            'dataUris' => null,
            'defaultValue' => null,
            'hide' => null,
            'hideSpeed' => null,
            'inline' => null,
            'letterCase' => null,
            'opacity' => null,
            'position' => null,
            'show' => null,
            'showSpeed' => null,
            'theme' => null,
        ));

        $resolver->setAllowedValues('control', array(null, 'hue', 'brightness', 'saturation', 'wheel'));
        $resolver->setAllowedValues('letterCase', array(null, 'uppercase', 'lowercase'));
        $resolver->setAllowedValues('position', array(null, 'bottom left', 'bottom right', 'top left', 'top right'));

        $resolver->setAllowedTypes('animationSpeed', array('null', 'integer'));
        $resolver->setAllowedTypes('changeDelay', array('null', 'integer'));
        $resolver->setAllowedTypes('dataUris', array('null', 'bool'));
        $resolver->setAllowedTypes('hideSpeed', array('null', 'integer'));
        $resolver->setAllowedTypes('inline', array('null', 'bool'));
        $resolver->setAllowedTypes('opacity', array('null', 'bool'));
        $resolver->setAllowedTypes('show', array('null', 'integer'));
        $resolver->setAllowedTypes('showSpeed', array('null', 'integer'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_util_colour';
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
