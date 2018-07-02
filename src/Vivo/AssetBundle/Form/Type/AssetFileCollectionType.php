<?php

namespace Vivo\AssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\AssetBundle\Form\EventListener\SingleAssetListener;

/**
 * Class AssetFileCollectionType.
 */
class AssetFileCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['multiple']) {
            $builder->addEventSubscriber(new SingleAssetListener());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['note'] = $options['note'];
        $view->vars['note_include_path'] = $options['note_include_path'];
        $view->vars['empty_text'] = $options['empty_text'];
        $view->vars['upload_route'] = $options['upload_route'];
        $view->vars['upload_params'] = $options['upload_params'];
        $view->vars['upload_expiry'] = $options['upload_expiry'] instanceof \DateTime ? $options['upload_expiry']->getTimestamp() : null;
        $view->vars['mime_types'] = $options['mime_types'];
        $view->vars['button_text'] = $options['button_text'];
        $view->vars['button_class'] = $options['button_class'];
        $view->vars['button_icon'] = $options['button_icon'];
        $view->vars['multiple'] = $options['multiple'];

        if (isset($options['entry_options']['data_class'])) {
            $view->vars['asset_class'] = isset($options['entry_options']['data_class']) ? $options['entry_options']['data_class'] : null;
        } else {
            // BC Collection Type
            $view->vars['asset_class'] = isset($options['options']['data_class']) ? $options['options']['data_class'] : null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'entry_type' => 'Vivo\AssetBundle\Form\Type\AssetFileType',
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'upload_route' => 'vivo_asset.asset.upload',
            'upload_params' => array('_format' => 'json'),
            'upload_expiry' => new \DateTime('+4 hours'),
            'mime_types' => array('*'),
            'button_text' => function (Options $options) {
                return 'Upload File'.(true === $options['multiple'] ? 's' : '');
            },
            'button_class' => 'btn-primary',
            'button_icon' => 'icon-white icon-arrow-up',
            'note_include_path' => '@VivoAsset/Form/_note.html.twig',
            'note' => null,
            'empty_text' => 'No files have been uploaded.',
            'multiple' => true,
        ));

        $resolver->setAllowedTypes('mime_types', array('array'));
        $resolver->setAllowedTypes('upload_expiry', array('\DateTime'));
        $resolver->setAllowedTypes('multiple', array('bool'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\CollectionType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_asset_asset_file_collection';
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
